<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use App\Models\BuildingInfo\Building;
use Illuminate\Support\Facades\Auth;
use App\Models\LayerInfo\Ward;
use Illuminate\Support\Facades\URL;
use App\Models\TaxPaymentInfo\DueYear;
use App\Models\BuildingInfo\FunctionalUse;
use App\Models\BuildingInfo\UseCategory;
use App\Models\UtilityInfo\Roadline;
use Illuminate\Support\Facades\Crypt;

use Illuminate\Support\Facades\DB;
use Illuminate\Http\Request;
use Illuminate\Support\Facades\Hash;
use App\Models\User;
use Illuminate\Support\Facades\Validator;
use Illuminate\Support\Facades\Cache;
use App\Http\Controllers\Api\AuthController;
class ViewMapController extends Controller
{

    /* login */
    public function mapLogin(Request $request)
    {
        try {
            // Create instance of AuthController
            $authController = new AuthController();
            $response = $authController->login($request);

            // Convert response to array to access the data
            $responseData = json_decode($response->getContent(), true);

            // If login was successful
            if ($response->getStatusCode() === 200 && isset($responseData['token'])) {
                // Find the user by email and update their api_token
                User::where('email', $request->email)->update([
                    'api_token' => $responseData['token']
                ]);

                return response()->json([
                    'status' => true,
                    'message' => 'Login successful and token saved',
                    'token' => $responseData['token'],
                    'data' => $responseData['data'] ?? null
                ]);
            }

            // If login failed, return the original response
            return $response;

        } catch (\Exception $e) {
            return response()->json([
                'status' => false,
                'message' => 'Error during login: ' . $e->getMessage()
            ], 500);
        }
    }

    public function verifyToken(Request $request)
    {
            try {
                // Get token from Authorization header
                $bearerToken = $request->bearerToken();

                if (!$bearerToken) {
                    return response()->json([
                        'status' => false,
                        'message' => 'No token provided in Authorization header'
                    ], 401);
                }

                // Find user with this token
                $user = User::where('api_token', $bearerToken)->first();

                if (!$user) {
                    return response()->json([
                        'status' => false,
                        'message' => 'Invalid or expired token'
                    ], 401);
                }

                // If we get here, token is valid
                return response()->json([
                    'status' => true,
                    'message' => 'Token is valid',
                    'user' => [
                        'email' => $user->email,
                        'name' => $user->name
                    ]
                ]);

            } catch (\Exception $e) {
                return response()->json([
                    'status' => false,
                    'message' => 'Error verifying token: ' . $e->getMessage()
                ], 500);
            }
    }

    public function accessMap(Request $request){
        #verify token
        $verifyToken = $this->verifyToken($request);
        #verifydata
        $verifyData = json_decode($verifyToken->getContent(), true);
        if (!$verifyData['status']){
            return response()->json([
                'status' => false,
                'message' => 'Invalid token'
            ], 401);
        }
        $user = User::where('api_token', $request->bearerToken())->first();
        if (!$user->hasRole('Municipality - Map Viewer')){
            return response()->json([
                'status' => false,
                'message' => 'Unauthorized'
            ], 401);
        }
        #get ebps id
        $ebpsId = $request->ebps_id;
        #get building
        $building = Building::where('ebps_id', $ebpsId)->first();
        if (!$building){
            return response()->json([
                'status' => false,
                'message' => 'Invalid EBPS ID'
            ], 401);
        }

        /* Get Bin number of sent ebps id */
        $bin = Building::select('bin')->where('ebps_id', $ebpsId)->first();
            $layer= "buildings_layer";
            $field= "bin";
            $val = $bin->bin;
            /* dd($val); */
        #generate map url
        $mapUrl = URL::temporarySignedRoute(
            'maps.show',
            now()->addMinutes(60),
            [
                /* 'ebps_id' => $ebpsId, */
                /* 'layer' => $layer, */
                'field' => $field,
                'val' => $val,
                'user_id' => $user->id,
                'token' => $request->bearerToken()
            ]
        );
        #return map url
        return response()->json([
            'status' => true,
            'message' => 'Map URL generated successfully',
            'map_url' => $mapUrl,

        ]);

    }
    public function showMap(Request $request){
        try {

            /* header('Access-Control-Allow-Origin: *'); */
            // Get the user based on user_id from the URL

            $user = User::find($request->user_id);
            /* dd($user); */
            if (!$user) {
                abort(403, 'User not found');
            }


            // Login the user
            Auth::login($user);

            #dd($val,$layer,$field);
            $layer = $request->layer;
            $field = $request->field;
            $val = $request->val;
            // Get building data
            $bldguse = FunctionalUse::orderBy('id', 'asc')->get(['name']);
            $usecatg = UseCategory::orderBy('id', 'asc')->get(['name']);
            $wards = Ward::orderBy('ward', 'asc')->pluck('ward', 'ward')->all();
            $dueYears = DueYear::orderBy('id', 'asc')->pluck('name', 'value')->all();
            // Determining max and min years
            $maxDate = date('Y');
            $minDate = date('Y') - 4;
            // Query to select distinct wards
            $pickWard ="select distinct ward from layer_info.wards order by ward asc";
            $pickWardResults = DB::select($pickWard);
            // Query to select distinct application dates
            $pickDate ="select distinct extract(year from application_date) as date1 from fsm.applications where deleted_at is null order by date1 DESC";
            $pickDateResults = DB::select($pickDate);
            // Query to select distinct structure types
            $pickStructureResults = DB::table('building_info.buildings')
                ->join('building_info.structure_types', 'building_info.structure_types.id', '=', 'building_info.buildings.structure_type_id')
                ->select('building_info.structure_types.*')
                ->groupBy('building_info.buildings.structure_type_id', 'building_info.structure_types.id')
                ->get();


            $page_title = "Map";

        // Fetching road hierarchy data
            $roadHierarchy = Roadline::whereNotNull('hierarchy')->groupBy('hierarchy')->pluck('hierarchy','hierarchy');
         // Fetching road surface types data
            $roadSurfaceTypes = Roadline::whereNotNull('surface_type')->groupBy('surface_type')->pluck('surface_type','surface_type');

            $bboxValues = DB::select("SELECT
            (ST_XMin(bbox) || ',' || ST_YMin(bbox) || ',' || ST_XMax(bbox) || ',' || ST_YMax(bbox)) AS bbox_values
            FROM (
                SELECT ST_Extent(geom) AS bbox FROM layer_info.citypolys
            ) AS extent_subquery
            ");
            $bboxstring = $bboxValues[0]->bbox_values;





            return view('maps.index-bin',compact('page_title', 'wards', 'dueYears', 'maxDate',
            'minDate', 'bldguse', 'usecatg', 'pickWardResults', 'pickDateResults', 'pickStructureResults', 'roadHierarchy', 'roadSurfaceTypes',
            'bboxstring','field','val'));

        } catch (\Exception $e) {
            // Log the full error details
            \Log::error('Map Access Error: ' . $e->getMessage());
            \Log::error('Stack trace: ' . $e->getTraceAsString());

            // For development, you can return detailed error
            return response()->json([
                'status' => false,
                'message' => 'Access Forbidden',
                'error' => $e->getMessage(),
                'file' => $e->getFile(),
                'line' => $e->getLine(),
                'trace' => $e->getTraceAsString()
            ], 403);

            // For production, use this instead:
            // abort(403, 'Access Forbidden: ' . $e->getMessage());
        }
    }

    }









