<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\FileNotFoundException as ContractFileNotFoundException;
use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\View\View;
use Illuminate\Support\Str;
use ZipArchive;

class FieldController extends Controller
{
    /**
     * @var Filesystem
     */
    private $filesystem;

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function fields(): View
    {
        $fields = [];
        foreach ($this->getClients() as $client) {
            $fields[$client] = $this->getFields($client);
        }

        return view('fields')->with('fields', $fields);
    }

    public function downloadField(string $fieldSlug)
    {
        if($field = $this->getField($fieldSlug)) {
            $zip = new ZipArchive;
            $fileName = 'field.zip';

            if ($zip->open(storage_path($fileName), ZipArchive::CREATE | ZipArchive::OVERWRITE) === TRUE) {
                $files = $this->filesystem->allFiles($field['name']);
                foreach ($files as $key => $value) {
                    $zip->addFile(config('appconfig.cerea_path') . '/Data/' . $value, $value);
                }
                $zip->close();
            }

            return response()->download(storage_path($fileName), date('Y-m-d_H-i') . '_' . $field['slug'] . '.zip')->deleteFileAfterSend();
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
