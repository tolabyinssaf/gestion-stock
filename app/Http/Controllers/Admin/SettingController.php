<?php

namespace App\Http\Controllers\Admin;

use App\Http\Controllers\Controller;
use App\Http\Requests\Admin\SettingRequest;
use App\Models\AppSetting;
use App\Services\ActivityLoggerService;
use Illuminate\Http\RedirectResponse;
use Illuminate\Support\Facades\Artisan;
use Illuminate\Support\Facades\Schema;
use Illuminate\View\View;

class SettingController extends Controller
{
    protected ActivityLoggerService $activityLogger;

    public function __construct(ActivityLoggerService $activityLogger)
    {
        $this->activityLogger = $activityLogger;
    }

    /**
     * Ensure settings table exists.
     */
    protected function ensureTableExists(): void
    {
        if (!Schema::hasTable('app_settings') || !Schema::hasTable('activity_logs')) {
            try {
                Artisan::call('migrate', ['--force' => true]);
            } catch (\Throwable $e) {
                // Ignore
            }
        }
    }

    /**
     * Display the settings page.
     */
    public function index(): View
    {
        $this->ensureTableExists();

        $settings = [
            'nom_entreprise' => AppSetting::get('nom_entreprise', 'IT Manager Pro Corp'),
            'nom_application' => AppSetting::get('nom_application', 'IT Manager Pro'),
            'adresse_entreprise' => AppSetting::get('adresse_entreprise', ''),
            'telephone_entreprise' => AppSetting::get('telephone_entreprise', ''),
            'email_contact' => AppSetting::get('email_contact', ''),

            'directeur_nom' => AppSetting::get('directeur_nom', 'Direction Générale'),
            'directeur_email' => AppSetting::get('directeur_email', 'direction@itmanager.local'),
            'signature_service_it' => AppSetting::get('signature_service_it', 'Service Informatique — IT Manager Pro'),

            'lignes_par_page' => AppSetting::get('lignes_par_page', 15),
            'format_date' => AppSetting::get('format_date', 'd/m/Y'),
            'fuseau_horaire' => AppSetting::get('fuseau_horaire', 'Europe/Paris'),
        ];

        return view('admin.settings.index', compact('settings'));
    }

    /**
     * Update the settings.
     */
    public function update(SettingRequest $request): RedirectResponse
    {
        $this->ensureTableExists();

        $validated = $request->validated();

        $oldValues = [
            'nom_entreprise' => AppSetting::get('nom_entreprise'),
            'nom_application' => AppSetting::get('nom_application'),
            'directeur_nom' => AppSetting::get('directeur_nom'),
            'directeur_email' => AppSetting::get('directeur_email'),
        ];

        // 1. Général
        AppSetting::set('nom_entreprise', $validated['nom_entreprise'] ?? '', 'general', 'Nom de l\'entreprise');
        AppSetting::set('nom_application', $validated['nom_application'], 'general', 'Nom de l\'application');
        AppSetting::set('adresse_entreprise', $validated['adresse_entreprise'] ?? '', 'general', 'Adresse de l\'entreprise');
        AppSetting::set('telephone_entreprise', $validated['telephone_entreprise'] ?? '', 'general', 'Téléphone de l\'entreprise');
        AppSetting::set('email_contact', $validated['email_contact'] ?? '', 'general', 'E-mail de contact');

        // 2. Gmail
        AppSetting::set('directeur_nom', $validated['directeur_nom'], 'email', 'Nom du directeur');
        AppSetting::set('directeur_email', $validated['directeur_email'], 'email', 'E-mail du directeur');
        AppSetting::set('signature_service_it', $validated['signature_service_it'] ?? '', 'email', 'Signature service IT');

        // 3. Préférences
        AppSetting::set('lignes_par_page', $validated['lignes_par_page'], 'preferences', 'Lignes par page');
        AppSetting::set('format_date', $validated['format_date'], 'preferences', 'Format de date');
        AppSetting::set('fuseau_horaire', $validated['fuseau_horaire'], 'preferences', 'Fuseau horaire');

        // Audit log
        $this->activityLogger->log(
            module: 'parametres',
            actionType: 'modification',
            description: 'Mise à jour des paramètres généraux de l\'application',
            entityType: 'AppSetting',
            entityId: null,
            entityName: 'Configuration globale',
            oldValues: $oldValues,
            newValues: [
                'nom_entreprise' => $validated['nom_entreprise'] ?? '',
                'nom_application' => $validated['nom_application'],
                'directeur_nom' => $validated['directeur_nom'],
                'directeur_email' => $validated['directeur_email'],
            ]
        );

        return redirect()->route('admin.settings.index')
            ->with('success', 'Les paramètres de l\'application ont été enregistrés avec succès.');
    }
}
