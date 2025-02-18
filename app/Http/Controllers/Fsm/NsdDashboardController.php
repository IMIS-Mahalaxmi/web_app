<?php

namespace App\Http\Controllers\Fsm;

use GuzzleHttp\Client;
use GuzzleHttp\Exception\RequestException;
use Illuminate\Http\Request;
use App\Http\Controllers\Controller;
use App\Models\Cwis\cwis_mne;

class NsdDashboardController extends Controller
{
    protected $client;
    private $bearerToken;
    private $email;
    private $password;
    private $apiLoginUrl;
    private $apiPostUrl;
    private $city;

    public function __construct()
    {
        $this->email = env('nsd_username');
        $this->password = env('nsd_password');
        $this->apiLoginUrl = env('API_LOGIN_URL');
        $this->apiPostUrl = env('API_POST_URL');
        $this->city = env('city');
    }

    public function pushToNsd($year)
    {
        if (empty($year)) {
            return redirect("cwis/cwis/getall")->with('error', 'Please select a year.');
        }

        // Validate if the year exists in CWIS records
        $all_years = cwis_mne::pluck('year')->toArray();
        if (!in_array($year, $all_years)) {
            return redirect()->back()->with('error', 'Please select a valid year from the dropdown.');
        }

        // Get authentication token for NSD
        $bearerToken = $this->getBearerToken();
        if (empty($bearerToken)) {
            return redirect()->back()->with('error', 'Failed to retrieve authentication token.');
        }

        // Fetch all published years and check if the selected year is already published
        $publishedYears = $this->checkNsdStatus();
        if (isset($publishedYears['error'])) {
            return redirect()->back()->with('error', 'Failed to retrieve published years.');
        }

        if (in_array($year, $publishedYears)) {
            return redirect()->back()->with('error', 'CWIS data for the selected year has already been published in the NSD.');
        }

        // Fetch CWIS data for the selected year
        $cwis = $this->getCwisData($year);
        if (empty($cwis)) {
            return redirect()->back()->with('error', 'Failed to retrieve CWIS data for the selected year.');
        }

        $response = $this->postToNSD($cwis, $year);

        // Handle API response
        if (isset($response['error'])) {
            return redirect()->back()->with('error', 'Failed to push CWIS data to NSD.');
        }

        return redirect()->back()->with('success', "CWIS data for $year pushed successfully to NSD! Please use the 'Check Status' button to verify the status.");
    }


    public function getBearerToken()
    { 
        try {
            if (!empty($this->bearerToken)) {
                return $this->bearerToken;
            }

            if (empty($this->email) || empty($this->password) || empty($this->apiLoginUrl)) {
                throw new \Exception("Missing required configuration for authentication.");
            }
            $client = new Client([
                'base_uri' => $this->apiLoginUrl,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                ],
            ]);
           
            $rawData = json_encode([
                'email' => $this->email,
                'password' => $this->password,
                'grant_type' => 'password',
            ]);
            $response = $client->post('api/v1/auth/authenticate', [
                'body' => $rawData, // Send the raw data here
            ]);

            $responseData = json_decode($response->getBody(), true);
            $this->bearerToken = $responseData['access_token']; // Store token for future use
           
            return $this->bearerToken;
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error fetching bearer token: ' . $e->getMessage());
            return null; // Return null or a default error message
        }
    } 
  
    public function getCwisData($year)
    {   
        try {
            $data = cwis_mne::SELECT('indicator_code as cwisCode','label as name','outcome','data_value as value')->where('year', $year)->get()->toArray();
            return $data;
           
        } catch (\Exception $e) {
            // Log the error
            \Log::error('Error fetching CWIS data: ' . $e->getMessage());
            return null; // Return null or a default error message
        }
        
    }

    public function postToNSD($cwis, $year)
    {
        $year = (integer)$year;
        $cwis_post_data = [
               "city"=> $this->city,
                "year"=> $year,
                "indicators"=> $cwis
        ];
        // Initialize the HTTP client
        $client = new Client([
            'base_uri' => $this->apiPostUrl,
            'headers' => [
                'Accept' => 'application/json',
                'Content-Type' => 'application/json', // Specify content type
                'Authorization' => 'Bearer ' . trim($this->bearerToken),
                'X-Requested-With' => 'XMLHttpRequest', // Common header to test
                'Content-Length' => strlen(json_encode($cwis_post_data)),  // Add content length header
                'Host' => parse_url($this->apiPostUrl, PHP_URL_HOST),
            ],
        ]);
       
        $response = $client->post('api/v1/imis/indicators', [
            'body' => json_encode($cwis_post_data), // Send the raw data here
        ]);

        $responseData = json_decode($response->getBody(), true);
        \Log::error('Error fetching bearer token: ' . $responseData);

        return $responseData;
    }

    public function checkNsdStatus()
    {
        try {
            $token = $this->getBearerToken();

            // Check if token retrieval was successful
            if (!$token) {
                throw new \Exception("Failed to retrieve bearer token.");
            }

            $client = new Client([
                'base_uri' => $this->apiPostUrl,
                'headers' => [
                    'Accept' => 'application/json',
                    'Content-Type' => 'application/json',
                    'Authorization' => 'Bearer ' . trim($token),
                    'X-Requested-With' => 'XMLHttpRequest',
                    'Host' => parse_url($this->apiPostUrl, PHP_URL_HOST),
                ],
            ]);

            $cities = is_array($this->city) ? $this->city : [$this->city];
            $publishedYears = [];
            
            // Loop through each city and make an API request
            foreach ($cities as $city) {
                $response = $client->get('api/v1/imis/indicators/metadata', [
                    'query' => [
                        'city' => $city,
                        'published_years' => "",
                        'draft_year' => "",
                    ],
                ]);
                
                // Decode the response body
                $responseData = json_decode($response->getBody(), true);
                return $responseData;
            }

            // Log the published years for debugging2
            \Log::info('Published Years: ', $publishedYears);

            return $publishedYears;  // Return only the published years
        } catch (\Exception $e) {
            \Log::error('Error fetching NSD status: ' . $e->getMessage());
            return ['error' => 'Failed to fetch NSD status.'];
        }
    }
}
