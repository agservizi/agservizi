<?php

namespace App\Http\Controllers\Backend;

use App\Models\RegistroLogin;
use Carbon\Carbon;
use Illuminate\Http\Request;
use Illuminate\Support\Collection;
use App\Http\Controllers\Controller;
use Illuminate\Support\Facades\Storage;
use Spatie\Backup\BackupDestination\Backup;
use Spatie\Backup\Helpers\Format;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatus;
use Spatie\Backup\Tasks\Monitor\BackupDestinationStatusFactory;

class RegistriController extends Controller
{


    /**
     * Display a listing of the resource.
     *
     * @return \Illuminate\Contracts\View\Factory|\Illuminate\View\View
     */
    public function index(Request $request, $cosa)
    {

        switch ($cosa) {
            case 'login':
                return $this->registroLogin($request);

            case 'modifiche':
                return $this->registroModifiche($request);

            case 'backup-db':
                if ($request->input('scarica')) {
                    $fileName = $request->input('scarica');
                    $path_to_file = '/backup-database/' . $fileName;
                    $headers = [
                        'Content-Type' => 'application/zip',
                        'Content-Disposition' => 'attachment; filename="' . $fileName . '"',
                    ];

                    return \Response::make(Storage::disk(config('backup.backup.destination.disks')[0])->get($path_to_file), 200, $headers);


                    return response()->download(Storage::disk(config('backup.destination.disks')[0])->get($path_to_file), $fileName);
                }
                if ($request->has('esegui')) {
                    \Artisan::call('backup:run --only-db --disable-notifications');
                }
                return $this->backupDatabase();

            case 'reset-cache':
                \Artisan::call('config:clear');
                \Artisan::call('route:cache');

                dd('ok');


        }

    }


    protected function registroLogin($request)
    {
        $filtro = false;
        $recordsQB = RegistroLogin::with('utente')->with('impersonatoDa')->orderBy('id', 'desc');
        if (\Auth::id() > 1) {
            $recordsQB->where('user_id', '>', 1)->where('impersonato_da_id', '>', 1);
        }

        if ($request->input('giorno')) {
            $recordsQB->whereDate('created_at', Carbon::createFromFormat('d/m/Y', $request->input('giorno')));
            $filtro = true;
        }

        if ($request->input('riuscito')) {
            $recordsQB->where('riuscito', $request->input('riuscito') - 10);
            $filtro = true;
        }
        if ($request->input('user_id')) {
            $recordsQB->where('user_id', $request->input('user_id'));
            $filtro = true;
        }

        $records = $recordsQB->paginate(100);
        if ($filtro) {
            $records->appends($_GET)->links();

        }

        return view('Backend.Registri.indexLogin')->with([
            'records' => $records,
            'filtro' => $filtro,
            'controller' => OperatoreController::class,
            'titoloPagina' => 'Elenco login'
        ]);

    }

    public function registroModifiche(Request $request)
    {
        $recordsQB = Audit::with(['user' => function ($q) {
            $q->select('id', 'name', 'cognome');
        }]);

        $orderBy = false;
        if ($request->has('ordine')) {
            $recordsQB->where('tags', 'ordine_' . $request->input('ordine'));
            $orderBy = true;

        }

        if ($request->input('id')) {
            $recordsQB->where('auditable_id', $request->input('id'));
        }

        if ($request->input('giorno')) {
            $recordsQB->whereDate('created_at', Carbon::createFromFormat('d/m/Y', $request->input('giorno')));
        }
        if ($orderBy == false) {
            $recordsQB->orderBy('id', 'desc');
        }

        return view('Backend.Registri.indexModifiche')->with([
            'records' => $recordsQB->paginate(100)->withQueryString()
        ]);


    }


    protected function backupDatabase()
    {
        $statuses = BackupDestinationStatusFactory::createForMonitorConfig(config('backup.monitor_backups'));
        list($headers, $rows) = $this->displayOverview($statuses);

        $files = collect(\Storage::disk(config('backup.backup.destination.disks')[0])->listContents('/backup-database'))->sortBy('basename');
        return view('Backend.Registri.showBackup', [
            'headers' => $headers,
            'rows' => $rows,
            'titoloPagina' => 'Registro backup database',
            'files' => $files
        ]);


    }

    protected function displayOverview(Collection $backupDestinationStatuses)
    {
        $headers = ['Nome', 'Disco', 'Raggiungibile', 'Integro', 'numero di backups', 'Ultimo backup', 'Spazio utilizzato'];

        $rows = $backupDestinationStatuses->map(function (BackupDestinationStatus $backupDestinationStatus) {
            return $this->convertToRow($backupDestinationStatus);
        });


        return [$headers, $rows];
    }

    public function convertToRow(BackupDestinationStatus $backupDestinationStatus): array
    {
        $destination = $backupDestinationStatus->backupDestination();

        $row = [
            $destination->backupName(),
            'disk' => $destination->diskName(),
            Format::emoji($destination->isReachable()),
            Format::emoji($backupDestinationStatus->isHealthy()),
            'amount' => $destination->backups()->count(),
            'newest' => $this->getFormattedBackupDate($destination->newestBackup()),
            'usedStorage' => Format::humanReadableSize($destination->usedStorage()),
        ];

        if (!$destination->isReachable()) {
            foreach (['amount', 'newest', 'usedStorage'] as $propertyName) {
                $row[$propertyName] = '/';
            }
        }

        if ($backupDestinationStatus->getHealthCheckFailure() !== null) {
            $row['disk'] = '<error>' . $row['disk'] . '</error>';
        }

        return $row;
    }

    protected function getFormattedBackupDate(Backup $backup = null)
    {
        return is_null($backup)
            ? 'Nessun backup'
            : $this::ageInDays($backup->date());
    }

    public static function ageInDays(Carbon $date): string
    {
        return $date->diffForHumans();
    }


}
