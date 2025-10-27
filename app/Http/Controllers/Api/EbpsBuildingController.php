<?php

namespace App\Http\Controllers\Api;

use App\Http\Controllers\Controller;
use Illuminate\Http\Request;

class EbpsBuildingController extends Controller
{
    //

    #need to process vacant land data
    public function getVacantLandData($buildingPermitId, Request $request)
    {
        try {
            // Get JSON data from the request
            $data = $request->all();

            // Log the received data for debugging
            \Log::channel('ebps')->info('Received Data from EBPS', [
                'building_permit_id' => $buildingPermitId,
                'received_data' => $data,
                'timestamp' => Carbon::now()->toDateTimeString()
            ]);

            // Add validation check before insertion
            $existingRecord = DB::table('ebps_flat_table.Ebps_building_info')
                ->where('bldgprmt_tid', $data['BldgPrmt_TID'])
                ->where('transaction_type', $data['TransactionType'])
                ->first();

            if ($existingRecord) {
                \Log::channel('ebps')->warning('Duplicate record attempt', [
                    'building_permit_id' => $buildingPermitId,
                    'transaction_type' => $data['TransactionType'],
                    'timestamp' => Carbon::now()->toDateTimeString()
                ]);

                return response()->json([
                    'success' => false,
                    'responseCode' => "400",
                    'content' => "Record already exists",
                    'error_code' => "DUPLICATE_RECORD",
                    'error_details' => "The combination of building permit ID and transaction type already exists"
                ], 400);
            }

            // Proceed with insertion if validation passes
            DB::table('ebps_flat_table.Ebps_building_info')->insert([
                'bldgprmt_tid' => $data['BldgPrmt_TID'] ?? null,  // Changed to match their format
                'application_number' => $data['ApplicationNumber'] ?? null,
                'tax_code' => $data['tax_code'] ?? null,
                'structure_type' => $data['structureType'] ?? null,
                'ward' => $data['Ward'] ?? null,
                'transaction_type' => $data['TransactionType'] ?? null,
                'lat_designer' => $data['Designer_Latitude'] ?? null,
                'long_designer' => $data['Designer_Longitude'] ?? null,
                'functional_use' => $data['buildingPurposeNm'] ?? null,
                'owner' => $data['HouseOwnerNm'] ?? null,
                'floor_count' => $data['NoOfStorey'] ?? null,
                'location' => $data['ToleName'] ?? null,
                'created_at' => Carbon::now(),
                'updated_at' => Carbon::now()
            ]);
            // Log success
            \Log::channel('ebps')->info('Data stored successfully', [
                'building_permit_id' => $buildingPermitId,
                'timestamp' => Carbon::now()->toDateTimeString()
            ]);

            // Prepare success response
            $successResponse = [
                'success' => true,
                'responseCode' => "200",
                'content' => "hello connected",
                'error_code' => null,
                'received_data' => [
                    'BldgPrmt_TID' => $data['BldgPrmt_TID'] ?? null,
                    'ApplicationNumber' => $data['ApplicationNumber'] ?? null,
                    'Ward' => $data['Ward'] ?? null,
                    'TransactionType' => $data['TransactionType'] ?? null,
                    'HouseOwnerNm' => $data['HouseOwnerNm'] ?? null,
                    'timestamp' => Carbon::now()->toDateTimeString()
                ]
            ];

             /* From building table */
             /* check if ebps id exists or not */
             /* once checked - store
            ebps_id-> bldgprmt_tid
            tax_code->tax_code
             */
            try{
                DB::beginTransaction();
                $existingBuilding = Building::where('ebps_id', $data['BldgPrmt_TID'])->first();
                if($existingBuilding){
                    $building = $existingBuilding;
                }
                else{
                    $maxBIN = Building::withTrashed()->max('bin');
                    $maxBIN = str_replace('B', '', $maxBIN);
                    $building = new Building();
                    $building->bin ='B' .sprintf('%06d', $maxBIN + 1);
                }
                $building->ebps_id = $data['BldgPrmt_TID'];
                $building->tax_code = $data['tax_code'];
                $building->save();
                DB::commit();
            }

            catch(\Throwable $th){
                DB::rollBack();
            }


            // Log the response being sent back
            \Log::channel('ebps')->info('Sending response back to EBPS', [
                'building_permit_id' => $buildingPermitId,
                'response' => $successResponse,
                'timestamp' => Carbon::now()->toDateTimeString()
            ]);


            /* return response()->json($successResponse); */
            response()->json($successResponse);
            $this->processVacantLandData($buildingPermitId, $data);

            return;

            /* return "Data Send Sucessfully"; */

        } catch (\Exception $e) {
            // Log error with received data
            \Log::channel('ebps')->error('Failed to store data', [
                'building_permit_id' => $buildingPermitId,
                'error' => $e->getMessage(),
                'received_data' => $request->all(),
                'timestamp' => Carbon::now()->toDateTimeString()
            ]);

            // For error case
            $errorResponse = [
                'success' => false,
                'responseCode' => "500",
                'content' => "not connected",
                'error_code' => "Exception",
                'received_data' => $data ?? null,
                'error_details' => $e->getMessage()
            ];

            // Log the error response
            \Log::channel('ebps')->error('Sending error response back to EBPS', [
                'building_permit_id' => $buildingPermitId,
                'response' => $errorResponse,
                'timestamp' => Carbon::now()->toDateTimeString()
            ]);

            return response()->json($errorResponse);
            /* return "Data not Send"; */
        }
    }






    public function storeBuildingInfo($ebps_id, $transaction_type, Request $request)
    {
        try {
            if ($transaction_type === 'ApplicationForVacantLand') {
                return $this->getVacantLandData($ebps_id, $request);
            } elseif ($transaction_type === 'SuperStructure') {
                return $this->getSuperStructureData($ebps_id, $request);
            } elseif ($transaction_type === 'Completion') {
                return $this->getBuildingComplitionData($ebps_id, $request);
            } else {
                return response()->json([
                    'success' => false,
                    'responseCode' => "400",
                    'error_code' => "INVALID_TRANSACTION_TYPE",
                    'message' => "The transaction type '$transaction_type' is not supported."
                ], 400);
            }
        } catch (\Exception $e) {
            \Log::channel('ebps')->error('Failed in storeBuildingInfo method', [
                'ebps_id' => $ebps_id,
                'transaction_type' => $transaction_type,
                'error' => $e->getMessage(),
                'timestamp' => Carbon::now()->toDateTimeString()
            ]);

            return response()->json([
                'success' => false,
                'responseCode' => "500",
                'error_code' => "EXCEPTION",
                'message' => "Internal Server Error",
                'error_details' => $e->getMessage()
            ], 500);
        }
    }
}
