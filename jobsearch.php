<?php
require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Talent\V4beta1\CommuteFilter;
use Google\Cloud\Talent\V4beta1\CommuteFilter\RoadTraffic;
use Google\Cloud\Talent\V4beta1\CommuteMethod;
use Google\Cloud\Talent\V4beta1\JobQuery;
use Google\Cloud\Talent\V4beta1\JobServiceClient;
use Google\Cloud\Talent\V4beta1\LocationFilter;
use Google\Cloud\Talent\V4beta1\RequestMetadata;

searchJobs();

/*
string(293) "Following fields are missing or invalid: requestMetadata.userId, requestMetadata.domain, 
requestMetadata.sessionId. (Possible reasons: non-numeric for double field, invalid language code, etc.)
. Request ID for tracking: 3ea15695-2c68-40e4-bbe2-813ea88cd33a:APAb7IRwun7wtLWP1GSuPYpYk160mLkx0g=="
*/

function searchJobs()
{
    $projectId = getenv("GOOGLE_CLOUD_PROJECT");
    $parent = JobServiceClient::projectName($projectId);

    $userId = "9001";
    $hashedUserId = hash("sha256", $userId);
    $sessionId = "abcd1234";
    $hashedSessionId = hash("sha256", $sessionId);
    $domain = "kkmym.com";
    $requestMetadata = new RequestMetadata();
    $requestMetadata->setUserId($hashedUserId);
    $requestMetadata->setSessionId($hashedUserId);
    $requestMetadata->setDomain($domain);

    $jobQuery = new JobQuery();

    // $jobQuery->setQuery("東京二十三区");
    // $jobQuery->setQuery("京都市内");

    /*
    $locationFilter = new LocationFilter();
    $omotesandoStationLatLng = ['latitude' => 35.6652554, 'longitude'=>139.7099034];
    $kyotoStationLatLng = ['latitude'=>34.9858534,'longitude'=>135.756578];
    $latlng = new Google\Type\LatLng($kyotoStationLatLng);
    $locationFilter->setLatLng($latlng);
    $jobQuery->setLocationFilters(array($locationFilter));
    */

    $commuteFilter = new CommuteFilter();
    $commuteFilter->setCommuteMethod(CommuteMethod::WALKING);
    $commuteFilter->setRoadTraffic(RoadTraffic::TRAFFIC_FREE);
    $duration = new Google\Protobuf\Duration(['seconds'=>3600,'nanos'=>0]);
    $commuteFilter->setTravelDuration($duration);
    $kyotoCoodinate = ['latitude'=>35.0074743,'longitude'=>135.7582058];
    $tokyoCoodinate = ['latitude'=>35.6652554, 'longitude'=>139.7099034];
    $losCoodinate = ['latitude'=>34.0061772,'longitude'=>-118.4860723];
    $latlng = new Google\Type\LatLng($losCoodinate);
    $commuteFilter->setStartCoordinates($latlng);
    $jobQuery->setCommuteFilter($commuteFilter);

    $optionalArgs = [
        'jobQuery' => $jobQuery,
    ];

    $client = new JobServiceClient();

    try {
        $pagedResponse = $client->searchJobs($parent, $requestMetadata, $optionalArgs);
        foreach ($pagedResponse->iteratePages() as $page) {
            foreach ($page as $element) {
                var_dump($element->getJob()->getTitle());
                var_dump($element->getJob()->getAddresses());
            }
        }
    } catch(Exception $exp) {
        var_dump($exp);
    } finally {
        $client->close();
    }
}
