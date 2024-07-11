<?php

use App\Commands\MakeApiRequest;
use App\Enums\ApiRequestMethods;
use Symfony\Component\Console\Command\Command;

use function Pest\Laravel\artisan;

it('can generate an api get request', function () {
    artisan(MakeApiRequest::class, [
        'name' => 'test',
        'api' => 'TestApi',
        '--force' => true,
    ])
        ->expectsQuestion('Request method?', ApiRequestMethods::GET->value)
        ->expectsQuestion('Does this request need arguments?', false)
        ->assertExitCode(Command::SUCCESS);

    expect(getcwd().'/Api/TestApi/Requests/GetTestRequest.php')->toBeFile()
        ->and(
            file_get_contents(getcwd().'/Api/TestApi/Requests/GetTestRequest.php')
        )->toContain('class GetTestRequest extends Request')
        ->toContain('protected Method $method = Method::GET;')
        ->toContain('return \'/--api_endpoint--\';');
});

it('can generate an api get request with arguments', function () {
    artisan(MakeApiRequest::class, [
        'name' => 'test',
        'api' => 'TestApi',
        '--force' => true,
    ])
        ->expectsQuestion('Request method?', ApiRequestMethods::GET->value)
        ->expectsQuestion('Does this request need arguments?', true)
        ->assertExitCode(Command::SUCCESS);

    expect(getcwd().'/Api/TestApi/Requests/GetTestRequest.php')->toBeFile()
        ->and(
            file_get_contents(getcwd().'/Api/TestApi/Requests/GetTestRequest.php')
        )->toContain('class GetTestRequest extends Request')
        ->toContain('protected Method $method = Method::GET;')
        ->toContain('public function __construct(protected readonly int $id) {');
});
