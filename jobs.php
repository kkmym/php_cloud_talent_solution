<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Talent\V4beta1\Job;
use Google\Cloud\Talent\V4beta1\JobServiceClient;

// createJob();
listJobs();

function createJob()
{
    //================================================
    // ProjectID
    //================================================
    $projectId = getenv("GOOGLE_CLOUD_PROJECT");
    $parent = JobServiceClient::projectName($projectId);

    //================================================
    // Job
    //================================================
    $companyId = "15a316bc-fdc0-4f90-9abb-959db5531228";
    $company = JobServiceClient::companyWithoutTenantName($projectId, $companyId);

    $jobId = "50003";

    $address = "230 Bicknell Ave, Santa Monica, CA 90405";

    $job = new Job();
    $job->setCompany($company);
    $job->setDescription("海外駐在");
    $job->setTitle("海外");
    $job->setRequisitionId($jobId);
    $job->setAddresses(array($address));
    
    //================================================
    // JobServiceClient
    //================================================
    $jobServiceClient = new JobServiceClient();

    try {
        $response = $jobServiceClient->createJob($parent, $job);
        var_dump($response);
    } catch (Exception $exp) {
        var_dump($exp);
    } finally {
        $jobServiceClient->close();
    }
}

function listJobs()
{
    // Init
    $projectId = getenv("GOOGLE_CLOUD_PROJECT");
    $parent = JobServiceClient::projectName($projectId);

    $companyId = "15a316bc-fdc0-4f90-9abb-959db5531228";
    $company = JobServiceClient::companyWithoutTenantName($projectId, $companyId);
    $filter = 'companyName="' . $company . '"';
    $optParams = [
        'filter' => $filter,
    ];

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes(
        array('https://www.googleapis.com/auth/jobs')
    );

    $cloudTalentSolutionClient = new Google_Service_CloudTalentSolution($client);

    try {
        $jobs = $cloudTalentSolutionClient->projects_jobs->listProjectsJobs($parent, $optParams);
        var_dump($jobs);
    } catch (Exception $exp) {
        var_dump($exp);
    }
}
