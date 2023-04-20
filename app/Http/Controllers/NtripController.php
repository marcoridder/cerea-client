<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class NtripController extends Controller
{

    /**
     * @var Filesystem
     */
    private $filesystem;

    private $fileName = 'ntripprofiles.txt';

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function index(): View
    {
        $ntripProfiles = $this->getNtripProfiles();

        return view('ntrip')
            ->with('ntripProfiles', $ntripProfiles)
        ;
    }

    public function save(Request $request): RedirectResponse
    {
        $data = $request->except('_token');
        $ntripProfileRows = [];
        foreach($data['profiles'] as $profile) {
            array_unshift($profile, '@SAVENTRIPPROFILE');
            array_push($profile, '@END');
            $ntripProfileRows[] = implode(',', $profile);
        }
        $this->filesystem->put($this->fileName, implode("\n", $ntripProfileRows));

        return redirect(route('ntrip.index'))->with('status', __('Saved'));
    }

    public function add(Request $request): RedirectResponse
    {
        $data = $request->except('_token');
        $ntripProfiles = $this->getNtripProfiles();
        if(in_array($data['name'], array_column($ntripProfiles, 'name'))) {
            return redirect(route('ntrip.index'))->with('status', __(':name already exists', ['name' => $data['name']]));
        }

        $ntripProfileRows = [];
        foreach ($ntripProfiles as $profile) {
            $ntripProfileRows[] = implode(',', $profile);
        }

        array_unshift($data, '@SAVENTRIPPROFILE');
        array_push($data, '@END');
        $ntripProfileRows[] = implode(',', $data);

        $this->filesystem->put($this->fileName, implode("\n", $ntripProfileRows));

        return redirect(route('ntrip.index'))->with('status', __('":name" has been added', ['name' => $data['name']]));
    }

    public function delete($name)
    {
        $ntripProfileRows = [];
        foreach ($this->getNtripProfiles() as $ntripProfile) {
            if ($ntripProfile['name'] === $name) {
                continue;
            }
            $ntripProfileRows[] = implode(',', $ntripProfile);
        }
        $this->filesystem->put($this->fileName, implode("\n", $ntripProfileRows));

        return redirect(route('ntrip.index'))->with('status', __('":name" is deleted', ['name' => $name]));
    }

    protected function getNtripProfiles(): array
    {
        $ntripProfiles = [];
        if ($this->filesystem->exists($this->fileName)) {
            $ntripProfilesString = $this->filesystem->get($this->fileName);
            foreach(array_filter(explode("\n", $ntripProfilesString)) as $ntripProfileRow) {
                list($start, $name, $host, $port, $mountpoint, $userName, $password, $end) = explode(',', $ntripProfileRow);
                $ntripProfiles[] = [
                    'start' => $start,
                    'name' => $name,
                    'host' => $host,
                    'port' => $port,
                    'mountpoint' => $mountpoint,
                    'userName' => $userName,
                    'password' => $password,
                    'end' => $end,
                ];
            }
        }

        return $ntripProfiles;
    }
}
