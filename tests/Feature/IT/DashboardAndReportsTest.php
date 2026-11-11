<?php

namespace Tests\Feature\IT;

use App\Models\Category;
use App\Models\DemandeMateriel;
use App\Models\Emplacement;
use App\Models\Equipement;
use App\Models\Mouvement;
use App\Models\Produit;
use App\Models\Stock;
use App\Models\User;
use App\Services\ExportService;
use App\Services\ReportingService;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Tests\TestCase;

class DashboardAndReportsTest extends TestCase
{
    use RefreshDatabase;

    protected User $itUser;
    protected User $adminUser;
    protected Produit $produit;
    protected Emplacement $emplacement;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itUser = User::factory()->create([
            'role' => User::ROLE_RESPONSABLE_IT,
            'status' => User::STATUS_ACTIVE,
            'name' => 'Jean Responsable IT',
        ]);

        $this->adminUser = User::factory()->create([
            'role' => User::ROLE_ADMIN,
            'status' => User::STATUS_ACTIVE,
            'name' => 'Sophie Admin',
        ]);

        $category = Category::create([
            'nom' => 'Serveurs & Réseau',
            'code' => 'SRV',
            'est_actif' => true,
        ]);

        $this->emplacement = Emplacement::create([
            'nom' => 'Salle Serveurs',
            'code' => 'SRV-01',
            'site' => 'Sous-sol',
            'est_actif' => true,
        ]);

        $this->produit = Produit::create([
            'designation' => 'Serveur Dell PowerEdge',
            'reference' => 'SRV-DELL-01',
            'type_suivi' => Produit::TYPE_INDIVIDUEL,
            'statut' => Produit::STATUT_ACTIF,
            'seuil_minimum' => 2,
            'category_id' => $category->id,
        ]);

        Equipement::create([
            'produit_id' => $this->produit->id,
            'emplacement_id' => $this->emplacement->id,
            'numero_serie' => 'SN-SRV-9999',
            'code_inventaire' => 'INV-SRV-01',
            'statut' => Equipement::STATUT_DISPONIBLE,
            'etat' => Equipement::ETAT_BON,
        ]);
    }

    public function test_it_dashboard_displays_real_mysql_stats(): void
    {
        $response = $this->actingAs($this->itUser)->get(route('it.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Tableau de bord Responsable IT');
        $response->assertSee('1 équipements suivis');
    }

    public function test_admin_dashboard_displays_supervision_metrics(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('admin.dashboard'));
        $response->assertStatus(200);
        $response->assertSee('Supervision Administrateur');
        $response->assertSee('Total comptes');
        $response->assertSee('Serveur Dell PowerEdge');
    }

    public function test_it_user_can_access_reports_hub(): void
    {
        $response = $this->actingAs($this->itUser)->get(route('it.reports.index'));
        $response->assertStatus(200);
        $response->assertSee('Rapports & Exports du parc informatique');
        $response->assertSee('Inventaire des équipements');
    }

    public function test_admin_cannot_access_it_reports(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('it.reports.index'));
        $response->assertStatus(403);
    }

    public function test_inventaire_report_filters_and_excel_export(): void
    {
        // 1. Web view
        $response = $this->actingAs($this->itUser)->get(route('it.reports.inventaire'));
        $response->assertStatus(200);
        $response->assertSee('SN-SRV-9999');

        // 2. Excel CSV stream export
        $exportResponse = $this->actingAs($this->itUser)->get(route('it.reports.inventaire', ['export' => 'excel']));
        $exportResponse->assertStatus(200);
        $this->assertEquals('text/csv; charset=UTF-8', $exportResponse->headers->get('Content-Type'));
        $this->assertStringContainsString('attachment; filename="inventaire_equipements_', $exportResponse->headers->get('Content-Disposition'));
    }

    public function test_stocks_report_filters_and_pdf_export(): void
    {
        // Add quantity stock
        $consommable = Produit::create([
            'designation' => 'Câble RJ45 Cat6',
            'reference' => 'CAB-RJ45',
            'type_suivi' => Produit::TYPE_QUANTITE,
            'statut' => Produit::STATUT_ACTIF,
            'seuil_minimum' => 10,
        ]);

        Stock::create([
            'produit_id' => $consommable->id,
            'emplacement_id' => $this->emplacement->id,
            'quantite' => 5, // Under threshold
        ]);

        // 1. Web view
        $response = $this->actingAs($this->itUser)->get(route('it.reports.stocks'));
        $response->assertStatus(200);
        $response->assertSee('Câble RJ45 Cat6');

        // 2. PDF export view
        $pdfResponse = $this->actingAs($this->itUser)->get(route('it.reports.stocks', ['export' => 'pdf']));
        $pdfResponse->assertStatus(200);
        $pdfResponse->assertSee('État des Stocks Informatiques');
        $pdfResponse->assertSee('Câble RJ45 Cat6');
    }

    public function test_export_service_sanitizes_formula_injection(): void
    {
        $dangerousInput = '=cmd|"/C calc"!A0';
        $sanitized = ExportService::sanitizeCell($dangerousInput);
        $this->assertEquals("'" . $dangerousInput, $sanitized);

        $safeInput = 'Dell Latitude 5540';
        $this->assertEquals('Dell Latitude 5540', ExportService::sanitizeCell($safeInput));
    }
}
