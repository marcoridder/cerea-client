<?php

namespace App\Http\Controllers;

use Illuminate\Support\Arr;
use Illuminate\Http\Client\Factory;

class UpdateController extends Controller
{

    private Factory $githubClient;

    private $basePath;

    public function __construct(Factory $githubClient)
    {
        $this->githubClient = $githubClient;
        $this->githubClient->timeout(1);
        $this->basePath = base_path();
    }

    public function update()
    {
        $latestRelease = $this->findLatestRelease();
        $latestReleaseTag = $latestRelease['tag_name'];
        $currentTag = $this->getCurrentTag();

        exec('cd '.$this->basePath.' && git fetch --tags');
        exec('cd '.$this->basePath.' && git tag', $localTags);

        if (!in_array($latestReleaseTag, $localTags)) {
            return back()->with('error', __('Failed to retrieve release'));
        }

        exec('cd '.$this->basePath.' && git checkout -f '.$latestReleaseTag.' && echo \'ok\'', $output);

        if (!in_array('ok', $output) || $this->getCurrentTag() !== $latestReleaseTag) {
            return back()->with('error', __('Update failed. Run manually').': cd '.$this->basePath.' && git checkout -f '.$latestReleaseTag);
        }

        $updateScripts = array_filter(array_map(function ($release) use ($currentTag) {
                if ($release['tag_name'] <= $currentTag) {
                    return null;
                }

                $updateScript = resource_path('updatescripts/' . $release['tag_name'] . '.sh');
                if (!file_exists($updateScript)) {
                    return null;
                }

                return [
                    'script' => $release['tag_name'] . '.sh',
                    'path' => $updateScript,
                ];
            }, array_reverse($this->findReleases()))
        );

        foreach ($updateScripts as $key => $updateScript) {
            unset($updateOutput);
            exec('cd ' . $this->basePath . ' && sh ' . $updateScript['path'], $updateOutput);
            if (!in_array('ok', $updateOutput)) {
                return back()->with('error', __('Update script failed at :script. Run manually', ['script' => $updateScript['script']]).': cd ' . resource_path('updatescripts/') . ' && sh ' . implode(' && sh ', array_column($updateScripts, 'script')));
            }
            unset($updateScripts[$key]);
        }

        return back()->with('status', __('Updated to version :version', ['version' => $this->getCurrentTag()]));
    }

    public function checkUpdate(): array
    {
        $latestRelease = $this->findLatestRelease();
        $latestReleaseTag = $latestRelease['tag_name'];

        $currentTag = $this->getCurrentTag();

        if ($latestReleaseTag === $currentTag) {
            return [
                'updateable' => false,
                'message' => __('No update available, we are working on it'),
            ];
        }

        return [
            'updateable' => true,
            'message' => __('Update available: :tag <br>Releasedate: :date', ['tag' => $latestReleaseTag, 'date' => date('d-m-Y', strtotime($latestRelease['published_at']))]),
        ];
    }

    private function findReleases(): array
    {
        try {
            $response = $this->githubClient->get('https://api.github.com/repos/marcoridder/cerea-client/releases');
            if ($response->successful()) {
                return $response->json();
            }
            throw new \Exception($response->json('message'));
        } catch (\Exception $exception) {
            report($exception);
            abort(500);
        }
    }

    private function findLatestRelease(): array
    {
        return Arr::first($this->findReleases());
    }

    private function getCurrentTag(): string
    {
        exec('cd '.$this->basePath.' && git describe --tags', $gitTagOutput);
        return $gitTagOutput[0];
    }
}
