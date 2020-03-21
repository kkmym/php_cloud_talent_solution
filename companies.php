<?php

require __DIR__ . '/vendor/autoload.php';

use Google\Cloud\Talent\V4beta1\CompanyServiceClient;
use Google\Cloud\Talent\V4beta1\Company;

// callSampleCreateCompany();
listCompanies();

function listCompanies()
{
    // Init
    $projectId = getenv("GOOGLE_CLOUD_PROJECT");
    $parent = CompanyServiceClient::projectName($projectId);

    $client = new Google_Client();
    $client->useApplicationDefaultCredentials();
    $client->setScopes(
        array('https://www.googleapis.com/auth/jobs')
    );

    $cloudTalentSolutionClient = new Google_Service_CloudTalentSolution($client);

    try {
        $companies = $cloudTalentSolutionClient->projects_companies->listProjectsCompanies($projectId, array('parent'=>$parent));
        var_dump($companies);
    } catch (Exception $exp) {
        var_dump($exp);
    }
}

function callSampleCreateCompany()
{
    $projectId = getenv("GOOGLE_CLOUD_PROJECT");
    $displayName = 'My Company 2';
    $externalId = '1003';
    sampleCreateCompany($projectId, $displayName, $externalId);
}

function sampleCreateCompany($projectId, $displayName, $externalId)
{
    $company = new Company();
    $company->setDisplayName($displayName);
    $company->setExternalId($externalId);

    $companyServiceClient = new CompanyServiceClient();
    $parent = CompanyServiceClient::projectName($projectId);

    try {
        $response = $companyServiceClient->createCompany(
            $parent
            ,$company
        );

        echo 'Company Created.';
        echo ' Name : '. $response->getName();
        echo ' DisplayName : '. $response->getDisplayName();
    } catch (Exception $exp) {
        var_dump($exp);
    } finally {
        $companyServiceClient->close();
    }
}
