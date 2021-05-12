<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Client\Factory;
use Illuminate\Support\Facades\Storage;
use ZipArchive;

class CereaController extends Controller
{
    /**
     * @var Filesystem
     */
    private $cereaFilesystem;
    private Factory $client;

    public function __construct(
        Filesystem $cereaFilesystem,
        Factory $client
    )
    {
        $this->cereaFilesystem = $cereaFilesystem;
        $this->client = $client;
        $this->client->timeout(1);
    }

    public function index(): View
    {
        $cereaVersions = [];
        try {
            $response = $this->client->get('https://cerea.marcoridder.nl/api/cerea-versions');
            if ($response->successful()) {
                $cereaVersions = $response->json();
            }
        } catch (\Exception $exception) {

        }

        return view('cerea')
            ->with('cereaVersions', $cereaVersions)
        ;
    }

    public function backup(): \Symfony\Component\HttpFoundation\BinaryFileResponse
    {
        $zip = new ZipArchive;
        $fileName = 'cerea.zip';

        if ($zip->open(storage_path($fileName), ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
            $files = $this->cereaFilesystem->allFiles();
            foreach ($files as $key => $value) {
                $zip->addFile(config('appconfig.cerea_path').'/'.$value, $value);
            }
            $zip->close();
        }

        return response()->download(storage_path($fileName), date('Y-m-d_H-i').'_cerea.zip')->deleteFileAfterSend();
    }

    public function appDownload(string $version)
    {
        $storage = Storage::disk('local');
        $fileName = 'com.cerea.Cerea'.$version.'.apk';

        try {
            if (!$storage->exists('cerea-versions/'.$fileName)) {
                $response = $this->client->get('https://cerea.marcoridder.nl/cerea-apk/' . $fileName);

                if (!$response->successful()) {
                    throw new \Exception('Dowloaden van app mislukt vanaf internet');
                }

                $storage->put('cerea-versions/' . $fileName, $response->body());
            }

            if ($storage->exists('cerea-versions/'.$fileName)) {
                return $storage->download('cerea-versions/'.$fileName, $fileName, ['Content-Type' => 'application/vnd.android.package-archive']);
            }
            throw new \Exception('App niet gevonden');

        } catch (\Exception $exception) {
            return back()->with('error', 'Dowloaden van app mislukt (' . $exception->getMessage() . ')');
        }
    }

}
