<?php
// Last Modified Date: 18-04-2024
//Developed By: Innovative Solution Pvt. Ltd. (ISPL)  (© ISPL, 2024)
namespace App\Http\Controllers\TaxPaymentInfo;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;
use App\Models\TaxPaymentInfo\TaxPaymentStatus;
use App\Models\LayerInfo\Ward;
use App\Models\TaxPaymentInfo\DueYear;
use DataTables;
use Illuminate\Support\Facades\DB as DB;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Storage;
use Illuminate\Support\Facades\Artisan;
use Box\Spout\Writer\Style\StyleBuilder;
use Box\Spout\Common\Type;
use Box\Spout\Writer\Style\Color;
use Box\Spout\Writer\WriterFactory;
use Box\Spout\Writer\AbstractWriter;
use Maatwebsite\Excel\Facades\Excel;
use App\Imports\TaxImport;
use Maatwebsite\Excel\HeadingRowImport;
use App\Models\TaxPaymentInfo\TaxPayment;

class TaxPaymentController extends Controller
{
    public function __construct()
    {
        $this->middleware('auth');
        $this->middleware('permission:List Property Tax Collection', ['only' => ['index']]);
        $this->middleware('permission:Import Property Tax Collection From CSV', ['only' => ['create', 'store']]);
        $this->middleware('permission:Export Property Tax Collection Info', ['only' => ['export', 'exportunmatched']]);

    }
    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Http\Response
     */
    public function index()
    {
        $page_title = __("Property Tax Collection");
        $wards = Ward::getInAscOrder();
        $dueYears = DueYear::getInAscOrder();

        return view('taxpayment-info.index', compact('page_title','wards', 'dueYears'));
    }
    /**
     * Prepare data for the DataTable.
     *
     * @param Request $request
     * @return DataTables
     * @throws Exception
     */
    public function getData(Request $request)
    {
        $buildingData = DB::table(DB::raw('(SELECT tax_code, bin, ward, owner_name, owner_contact, due_year 
            FROM taxpayment_info.tax_payment_status
            ORDER BY tax_code) tax'))
            ->leftjoin('taxpayment_info.due_years AS due', 'due.value', '=', 'tax.due_year')
            ->select('tax.*', 'due.name', 'tax.bin');

        return DataTables::of($buildingData)
            ->filter(function ($query) use ($request) {
                if ($request->dueyear_select) {
                    $query->where('due.name', $request->dueyear_select);
                }
                if ($request->ward_select) {
                    $query->where('tax.ward', $request->ward_select);
                }
                if ($request->tax_code) {
                    $query->where('tax_code', 'ILIKE', '%' . $request->tax_code . '%');
                }
                if ($request->bin) {
                    $query->where('tax.bin', 'ILIKE', '%' . $request->bin . '%');
                }
            })
            ->make(true);


    }
    /**
     * Display the form for creating a new resource.
     *
     * @return \Illuminate\View\View
     */
    public function create()
    {
        $page_title = __("Import Property Tax Collection Information Support System");
        return view('taxpayment-info.create', compact('page_title'));
    }
    /**
     * Store a newly created resource in storage.
     *
     * @param  \Illuminate\Http\Request  $request
     * @return \Illuminate\Http\RedirectResponse
     */
    public function store(Request $request)
    {
        Validator::extend('file_extension', function ($attribute, $value, $parameters, $validator) {
                if( !in_array( $value->getClientOriginalExtension(), $parameters ) ){
                return false;
            }
            else {
                return true;
            }
        }, __('File must be csv format.') );
        $this->validate($request,
                ['csvfile' => 'required|file_extension:csv'],
                ['required' => __('The csv file is required.')],
        );

        if (!$request->hasfile('csvfile')) {

            return redirect('tax-payment/data')->with('error',__('The csv file is required.'));
        }
        if ($request->hasFile('csvfile')) {

                $filename = 'building-tax-payments.' . $request->file('csvfile')->getClientOriginalExtension();
                if (Storage::disk('importtax')->exists('/' . $filename)){
                    Storage::disk('importtax')->delete('/' . $filename);
                    //deletes if already exists
                }
                $stored = $request->file('csvfile')->storeAs('/', $filename, 'importtax');

                if ($stored)
                {
                    $storage = Storage::disk('importtax')->path('/');
                    $location = preg_replace('/\\\\/', '', $storage);

                    $file_selection = Storage::disk('importtax')->listContents();
                    $filename = $file_selection[0]['basename'];

                    //checking csv file has all heading row keys
                    $headings = (new HeadingRowImport)->toArray($location.$filename);
                    $heading_row_errors = array();
                    if (!in_array("tax_code", $headings[0][0])) {
                        $heading_row_errors['tax_code'] = "Heading row : tax_code is required";
                    }
                    if (!in_array("owner_name", $headings[0][0])) {
                        $heading_row_errors['owner_name'] = "Heading row : owner_name is required";
                    }
                    if (!in_array("owner_contact", $headings[0][0])) {
                        $heading_row_errors['owner_contact'] = "Heading row : owner_contact is required";
                    }
                    if (!in_array("last_payment_date", $headings[0][0])) {
                        $heading_row_errors['last_payment_date'] = "Heading row : last_payment_date is required";
                    }
                    if (count($heading_row_errors) > 0) {
                    return back()->withErrors($heading_row_errors);
                    }
                    \DB::statement('TRUNCATE TABLE taxpayment_info.tax_payments RESTART IDENTITY');
                    #\DB::statement('ALTER SEQUENCE IF exists taxpayment_info.tax_payments_id_seq RESTART WITH 1');
                    $import = new TaxImport();
                    $import->import($location.$filename);

                    $message = __('Successfully Imported Building Tax Payments From CSV.');
                    \DB::statement("select taxpayment_info.fnc_taxpaymentstatus()");

                    \DB::statement('select taxpayment_info.fnc_updonimprt_gridnward_tax()');

                    return redirect('tax-payment')->with('success',$message);

                }
                else{
                    $message = __('Building Tax Payments Not Imported From CSV.');
                }

        }
        flash('Could not import from CSV. Try Again');
        return redirect('tax-payment');
    }
    /**
     * Export building tax payment data to a CSV file.
     *
     * @return \Illuminate\Http\Response
     */
    public function export()
    {
        $ward = $_GET['ward'] ?? null;
        $due_year = $_GET['due_year'] ?? null;
        $tax_code = $_GET['tax_code'] ?? null;
        $bin = $_GET['bin'] ?? null;

        $columns = [
            __('Tax Code'),
            __('BIN'),
            __('Ward'),
            __('Owner Name'),
            __('Owner Contact'),
            __('Due Years')
        ];

        $query = DB::table('taxpayment_info.tax_payment_status AS tax')
                                ->leftjoin('taxpayment_info.due_years AS due', 'due.value', '=', 'tax.due_year')
                                ->select('tax.*', 'due.name', 'tax.bin')
                                ->where('tax.deleted_at', null)
                                ->orderBy('tax.tax_code', 'ASC');

        if (!empty($ward)) {
            $query->where('tax.ward', $ward);
        }
        if (!empty($due_year)) {
            $query->where('due.name', $due_year);
        }
        if (!empty($tax_code)) {
            $query->where('tax.tax_code', 'ILIKE', '%' . $tax_code . '%');
        }
        if (!empty($bin)) {
            $query->where('tax.bin', 'ILIKE', '%' . $bin . '%');
        }
        
        $style = (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(13)
            ->setBackgroundColor(Color::rgb(228, 228, 228))
            ->build();

        $writer = WriterFactory::create(Type::CSV);
        $writer->openToBrowser('Property Tax Collection Information Support System.csv')
            ->addRowWithStyle($columns, $style); //Top row of CSV

        $query->chunk(5000, function ($taxpayments) use ($writer) {
            foreach($taxpayments as $taxpayment) {

                $values = [];
                $values[] = $taxpayment->tax_code;
                $values[] = $taxpayment->bin;
                $values[] = $taxpayment->ward;
                $values[] = $taxpayment->owner_name;
                $values[] = $taxpayment->owner_contact;
                $values[] = $taxpayment->name;
                $writer->addRow($values);
            }
        });

        $writer->close();
    }
    public function exportunmatched()
    {
        $columns = [
            __('Tax Code'),
            __('Owner Name'),
            __('Owner Contact'),
            __('Last Payment date')
        ];

        $query = DB::table('taxpayment_info.tax_payments AS tax')
                 ->leftjoin('building_info.buildings as b', 'tax.tax_code', '=', 'b.tax_code')
                 ->select('tax.*')
                 ->whereNull('b.tax_code')
                 ->orderBy('tax.tax_code', 'ASC');    
       
        $style = (new StyleBuilder())
            ->setFontBold()
            ->setFontSize(13)
            ->setBackgroundColor(Color::rgb(228, 228, 228))
            ->build();

        $writer = WriterFactory::create(Type::CSV);
        $writer->openToBrowser('Unmatched Records-Property Tax Collection Information Support System.csv')
            ->addRowWithStyle($columns, $style); //Top row of CSV

        $query->chunk(5000, function ($taxpayments) use ($writer) {
            foreach($taxpayments as $taxpayment) {
                $values = [];
                $values[] = $taxpayment->tax_code;
                $values[] = $taxpayment->owner_name;
                $values[] = $taxpayment->owner_contact;
                $values[] = $taxpayment->last_payment_date;
                $writer->addRow($values);
            }
        });

        $writer->close();
    }
}
