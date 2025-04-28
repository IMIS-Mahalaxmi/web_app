Version: V1.0.0

# NSD Api

The External API Service allows users to insert and publish indicator data into the National Sanitation Dashboard (NSD).

### NsdDashboardController

app\\Http\\Controllers\\Fsm\\NsdDashboardController.php

The controllers main function is to validate and verify the data to push to the NSD.

The basic classes of the controller are:

| **Function**    | \__construct()                                                                           |
|-----------------|------------------------------------------------------------------------------------------|
| **Description** | Initializes authentication credentials and API endpoints from environment variables.     |
| **Parameters**  |                                                                                          |
| **Return**      | null                                                                                     |
| **Source**      | app\\Http\\Controllers\\Fsm\\NsdDashboardController.php                                  |

| **Function**    | pushtoNsd()                                                                        |
|-----------------|------------------------------------------------------------------------------------|
| **Description** | Pushes CWIS data for the given year to NSD after validation and authentication     |
| **Parameters**  | \$year                                                                             |
| **Return**      | Success or error message                                                           |
| **Source**      | app\\Http\\Controllers\\Fsm\\NsdDashboardController.php                            |

| **Function**    | getBearerToken()                                                       |
|-----------------|------------------------------------------------------------------------|
| **Description** | Handles process of getting and storing bearer token                    |
| **Parameters**  |                                                                        |
| **Return**      | null                                                                   |
| **Source**      | app\\Http\\Controllers\\Fsm\\NsdDashboardController.php                |

| **Function**    | getCwisData()                                                          |
|-----------------|------------------------------------------------------------------------|
| **Description** | Handles process of getting CWIS Data from db                           |
| **Parameters**  | \$year                                                                 |
| **Return**      | null                                                                   |
| **Source**      | app\\Http\\Controllers\\Fsm\\NsdDashboardController.php                |

| **Function**    | postToNSD()                                                            |
|-----------------|------------------------------------------------------------------------|
| **Description** | Sends CWIS data for the specified year to NSD                          |
| **Parameters**  | \$year, \$year                                                         |
| **Return**      | null                                                                   |
| **Source**      | app\\Http\\Controllers\\Fsm\\NsdDashboardController.php                |

| **Function**    | checkNsdStatus()                                                       |
|-----------------|------------------------------------------------------------------------|
| **Description** | Checks the status of Indicators in NSD                                 |
| **Parameters**  |                                                                        |
| **Return**      | Returns data of Indicators                                             |
| **Source**      | app\\Http\\Controllers\\Fsm\\NsdDashboardController.php                |

### Remarks

Need to add credentials in env file for username, password and city.


