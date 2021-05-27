<?php

namespace App\Http\Controllers;

use Illuminate\Contracts\Filesystem\Filesystem;
use Illuminate\Contracts\View\View;
use Illuminate\Http\RedirectResponse;
use Illuminate\Http\Request;

class SettingsController extends Controller
{

    /**
     * @var Filesystem
     */
    private $filesystem;

    private $configItems = [
        [
            'key' => 'system_name',
            'name' => 'Systeem naam',
        ],
        [
            'key' => 'cerea_path',
            'name' => 'Cerea locatie',
            'default' => '/home/pi/Cerea30050hz/',
        ],
    ];

    public function __construct(Filesystem $filesystem)
    {
        $this->filesystem = $filesystem;
    }

    public function index(): View
    {
        $config = config('appconfig');

        exec('cd '.base_path().' && git describe --tags', $gitTagOutput);

        return view('settings')
            ->with('configItems', $this->configItems)
            ->with('config', collect($config))
            ->with('cereaClientVersion', $gitTagOutput[0] ?? null)
            ->with('languages', config('locale')['languages'])
            ->with('activeLanguage', $config['locale'] ?? config('app.fallback_locale'))
        ;
    }

    public function save(Request $request): RedirectResponse
    {
        $data = $request->except('_token');

        $appConfig = config('appconfig') ?? [];
        $appConfig = array_merge($appConfig, $data);

        $this->filesystem->put('appconfig.php', "<?php\n return ".var_export($appConfig, 1)." ;");

        return redirect(route('settings.index'))->with('status', __('Saved'));
    }
}
