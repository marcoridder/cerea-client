<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\FileNotFoundException as ContractFileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\Request;
use Illuminate\Support\Str;
use ZipArchive;

class FieldController extends Controller
{
    protected ZipArchive $zipArchive;
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(
        Filesystem $filesystem,
        ZipArchive $zipArchive
    )
    {
        $this->filesystem = $filesystem;
        $this->zipArchive = $zipArchive;
    }

    public function fields(): View
    {
        $fields = [];
        foreach ($this->getClients() as $client) {
            $fields[$client] = $this->getFields($client);
        }

        return view('fields')->with('fields', $fields);
    }

    public function uploadField(Request $request)
    {
        try {
            $file = $request->file('file');
            $this->zipArchive->open($file->getRealPath());

            for ($i = 0; $i < $this->zipArchive->numFiles; $i++) {
                $filename = $this->zipArchive->getNameIndex($i);
                if (basename($filename) === 'patterns.txt') {
                    [$clientName, $fieldName] = explode('/', $filename);

                    if (
                        app()->environment('production')
                        && file_exists(config('appconfig.cerea_path') . "/Data/$clientName")
                    ) {
                        exec('sudo chown pi:pi -R '.config('appconfig.cerea_path') . "/Data/$clientName");
                    }

                    $this->zipArchive->extractTo(config('appconfig.cerea_path') . '/Data/', $filename);
                    $this->zipArchive->close();

                    return redirect()->back()->with('status', __("Field :clientName/:fieldName uploaded", ['clientName' => $clientName, 'fieldName' => $fieldName]));
                }
            }
            throw new \Exception(__('No patterns.txt found'));
        } catch (\ValueError | \Exception $exception) {
            return redirect()->back()->with('error', __('Upload field failed (:message)', ['message' => $exception->getMessage()]));
        }
    }

    public function downloadField(string $fieldSlug)
    {
        if($field = $this->getField($fieldSlug)) {
            $zip = $this->zipArchive;
            $fileName = 'field.zip';

            if ($zip->open(storage_path($fileName), ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                $files = $this->filesystem->allFiles($field['name']);
                foreach ($files as $file) {
                    if(basename($file) === 'patterns.txt') {
                        $zip->addFile(config('appconfig.cerea_path') . '/Data/' . $file, $file);
                        $zip->close();
                        return response()->download(storage_path($fileName), date('Y-m-d_H-i') . '_' . $field['slug'] . '.zip')->deleteFileAfterSend();
                    }
                }
            }
        }
        abort(404);
    }

    public function editField(string $fieldSlug): View
    {
        if($field = $this->getField($fieldSlug)) {
            return view('field')
                ->with('field', $field)
                ->with('patterns', $this->filesystem->get($field['name'].'/patterns.txt'))
            ;
        }
        abort(404);
    }

    protected function getFields(string $client): array
    {
        $fields = $this->filesystem->directories($client);

        return array_map(function($field) {
            return [
                'name' => $field,
                'slug' => $slug = Str::slug(str_replace('/', '-', $field)),
                'downloadUrl' => route('field.download', [$slug]),
//                'editUrl' => route('field.edit', [$slug]),
                'patterns' => $this->getPatterns($field)
            ];
        }, $fields);
    }

    protected function getClients(): array
    {
        return $this->filesystem->directories();
    }

    protected function getField(string $fieldSlug): ?array
    {
        foreach ($this->getClients() as $client) {
            foreach ($this->getFields($client) as $field) {
                if ($field['slug'] === $fieldSlug) {
                    return $field;
                }
            }
        }

        return null;
    }

    protected function getPatterns(string $field): array
    {
        try {
            $patterns = $this->filesystem->get($field.'/patterns.txt');
        } catch (ContractFileNotFoundException $exception) {
            return [];
        }

        preg_match_all('/\,[\d]\,(.*?)\,/m', $patterns , $names);
        return $names[1];
    }
}
