<?php

use App\Commands\MakeApiRequest;
use App\Enums\ApiRequestMethods;
use Illuminate\Support\Facades\File;
use Symfony\Component\Console\Command\Command;

use function Pest\Laravel\artisan;

it('can generate an api get request', function () {
    $file = getcwd().'/module-test/src/Api/Test/Requests/GetTestRequest.php';
    File::delete($file);

    artisan(MakeApiRequest::class, [
        'name' => 'test',
        'api' => 'Test',
        '--force' => true,
    ])
        ->expectsQuestion('Request method?', ApiRequestMethods::GET->value)
        ->expectsQuestion('Does this request need arguments?', false)
        ->assertExitCode(Command::SUCCESS);

    expect($file)->toBeFile()
        ->and(
            file_get_contents($file)
        )
        ->toContain('namespace PrestaShop\Module\ModuleTest\Api\Test\Requests;')
        ->toContain('class GetTestRequest extends Request')
        ->toContain('protected Method $method = Method::GET;')
        ->toContain('return \'/--api_endpoint--\';');
});

it('can generate an api get request with arguments', function () {
    $file = getcwd().'/module-test/src/Api/Test/Requests/GetTestRequest.php';
    File::delete($file);

    artisan(MakeApiRequest::class, [
        'name' => 'test',
        'api' => 'Test',
        '--force' => true,
    ])
        ->expectsQuestion('Request method?', ApiRequestMethods::GET->value)
        ->expectsQuestion('Does this request need arguments?', true)
        ->assertExitCode(Command::SUCCESS);

    expect($file)->toBeFile()
        ->and(
            file_get_contents($file)
        )
        ->toContain('namespace PrestaShop\Module\ModuleTest\Api\Test\Requests;')
        ->toContain('class GetTestRequest extends Request')
        ->toContain('protected Method $method = Method::GET;')
        ->toContain('public function __construct(protected readonly int $id) {');
});
