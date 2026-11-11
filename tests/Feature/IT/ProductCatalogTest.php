<?php

namespace Tests\Feature\IT;

use App\Models\Categorie;
use App\Models\Produit;
use App\Models\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Http\UploadedFile;
use Illuminate\Support\Facades\Storage;
use Tests\TestCase;

class ProductCatalogTest extends TestCase
{
    use RefreshDatabase;

    protected User $itUser;
    protected User $adminUser;

    protected function setUp(): void
    {
        parent::setUp();

        $this->itUser = User::factory()->responsableIt()->create();
        $this->adminUser = User::factory()->admin()->create();
    }

    // -------------------------------------------------------------
    // 1. Accès & Protection des routes
    // -------------------------------------------------------------

    public function test_guest_is_redirected_to_login_when_accessing_catalog(): void
    {
        $response = $this->get(route('it.products.index'));
        $response->assertRedirect(route('login'));

        $catResponse = $this->get(route('it.categories.index'));
        $catResponse->assertRedirect(route('login'));
    }

    public function test_admin_cannot_access_it_catalog_routes(): void
    {
        $response = $this->actingAs($this->adminUser)->get(route('it.products.index'));
        $response->assertStatus(403);

        $catResponse = $this->actingAs($this->adminUser)->get(route('it.categories.index'));
        $catResponse->assertStatus(403);
    }

    // -------------------------------------------------------------
    // 2. Gestion des Catégories
    // -------------------------------------------------------------

    public function test_responsable_it_can_view_categories_list(): void
    {
        Categorie::create([
            'nom' => 'Ordinateurs portables',
            'icon_name' => 'laptop',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        $response = $this->actingAs($this->itUser)->get(route('it.categories.index'));

        $response->assertStatus(200);
        $response->assertSee('Catégories de matériel');
        $response->assertSee('Ordinateurs portables');
    }

    public function test_responsable_it_can_create_a_category(): void
    {
        $response = $this->actingAs($this->itUser)->post(route('it.categories.store'), [
            'nom' => 'Serveurs & Baies',
            'icon_name' => 'server',
            'description' => 'Serveurs racks et tours',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        $response->assertRedirect(route('it.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'nom' => 'Serveurs & Baies',
            'slug' => 'serveurs-baies',
            'icon_name' => 'server',
            'statut' => Categorie::STATUT_ACTIF,
        ]);
    }

    public function test_responsable_it_can_update_a_category(): void
    {
        $category = Categorie::create([
            'nom' => 'Écrans',
            'icon_name' => 'monitor',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        $response = $this->actingAs($this->itUser)->put(route('it.categories.update', $category), [
            'nom' => 'Écrans & Moniteurs',
            'icon_name' => 'monitor',
            'description' => 'Moniteurs de bureau et d\'accueil',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        $response->assertRedirect(route('it.categories.index'));
        $response->assertSessionHas('success');

        $this->assertDatabaseHas('categories', [
            'id' => $category->id,
            'nom' => 'Écrans & Moniteurs',
            'slug' => 'ecrans-moniteurs',
        ]);
    }

    public function test_responsable_it_can_toggle_category_status(): void
    {
        $category = Categorie::create([
            'nom' => 'Tablettes',
            'icon_name' => 'tablet',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        $response = $this->actingAs($this->itUser)->patch(route('it.categories.toggle-status', $category));

        $response->assertRedirect();
        $this->assertEquals(Categorie::STATUT_ARCHIVE, $category->fresh()->statut);

        // Toggle back to active
        $response2 = $this->actingAs($this->itUser)->patch(route('it.categories.toggle-status', $category));
        $this->assertEquals(Categorie::STATUT_ACTIF, $category->fresh()->statut);
    }

    public function test_responsable_it_cannot_delete_category_with_products(): void
    {
        $category = Categorie::create([
            'nom' => 'Ordinateurs portables',
            'icon_name' => 'laptop',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        Produit::create([
            'categorie_id' => $category->id,
            'nom' => 'ThinkPad T14',
            'marque' => 'Lenovo',
            'reference' => 'PC-LEN-001',
            'type_suivi' => Produit::TYPE_INDIVIDUEL,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        $response = $this->actingAs($this->itUser)->delete(route('it.categories.destroy', $category));

        $response->assertRedirect();
        $response->assertSessionHas('error');
        $this->assertDatabaseHas('categories', ['id' => $category->id]);
    }

    public function test_responsable_it_can_delete_empty_category(): void
    {
        $category = Categorie::create([
            'nom' => 'Périphériques obsolètes',
            'icon_name' => 'wrench',
            'statut' => Categorie::STATUT_ARCHIVE,
        ]);

        $response = $this->actingAs($this->itUser)->delete(route('it.categories.destroy', $category));

        $response->assertRedirect(route('it.categories.index'));
        $response->assertSessionHas('success');
        $this->assertDatabaseMissing('categories', ['id' => $category->id]);
    }

    // -------------------------------------------------------------
    // 3. Gestion du Catalogue Produits
    // -------------------------------------------------------------

    public function test_responsable_it_can_view_product_catalog(): void
    {
        $category = Categorie::create([
            'nom' => 'Ordinateurs portables',
            'icon_name' => 'laptop',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        $produit = Produit::create([
            'categorie_id' => $category->id,
            'nom' => 'Dell Latitude 5440',
            'marque' => 'Dell',
            'modele' => '5440',
            'reference' => 'PC-DEL-5440',
            'type_suivi' => Produit::TYPE_INDIVIDUEL,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        $response = $this->actingAs($this->itUser)->get(route('it.products.index'));

        $response->assertStatus(200);
        $response->assertSee('Catalogue du matériel informatique');
        $response->assertSee('Dell Latitude 5440');
        $response->assertSee('PC-DEL-5440');
    }

    public function test_responsable_it_can_render_product_create_page(): void
    {
        $response = $this->actingAs($this->itUser)->get(route('it.products.create'));

        $response->assertStatus(200);
        $response->assertSee('Référencer un nouveau matériel');
        $response->assertSee('Suivi individuel sérialisé');
        $response->assertSee('Suivi quantitatif en lot');
    }

    public function test_responsable_it_can_create_a_serialized_product(): void
    {
        Storage::fake('public');

        $category = Categorie::create([
            'nom' => 'Ordinateurs',
            'icon_name' => 'laptop',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        $photo = UploadedFile::fake()->image('laptop.jpg');

        $response = $this->actingAs($this->itUser)->post(route('it.products.store'), [
            'nom' => 'MacBook Pro 14 M3',
            'marque' => 'Apple',
            'modele' => 'M3 Pro',
            'reference' => 'MAC-M3-14',
            'categorie_id' => $category->id,
            'type_suivi' => Produit::TYPE_INDIVIDUEL,
            'description' => 'Poste pour développeurs et designers',
            'caracteristiques' => "CPU: M3 Pro 12-core\nRAM: 36 Go\nSSD: 1 To",
            'photo' => $photo,
            'prix_indicatif' => 2499.00,
            'seuil_minimum' => 3,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        $this->assertDatabaseHas('produits', [
            'nom' => 'MacBook Pro 14 M3',
            'reference' => 'MAC-M3-14',
            'marque' => 'Apple',
            'type_suivi' => Produit::TYPE_INDIVIDUEL,
            'prix_indicatif' => 2499.00,
            'seuil_minimum' => 3,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        $produit = Produit::where('reference', 'MAC-M3-14')->first();
        $this->assertNotNull($produit);
        $this->assertNotNull($produit->photo);
        Storage::disk('public')->assertExists($produit->photo);

        $response->assertRedirect(route('it.products.show', $produit));
    }

    public function test_responsable_it_can_create_a_quantity_product(): void
    {
        $category = Categorie::create([
            'nom' => 'Câbles & Adaptateurs',
            'icon_name' => 'cable',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        $response = $this->actingAs($this->itUser)->post(route('it.products.store'), [
            'nom' => 'Câble USB-C vers HDMI 2.0',
            'marque' => 'Anker',
            'modele' => 'A8177',
            'reference' => 'CAB-USBC-HDMI',
            'categorie_id' => $category->id,
            'type_suivi' => Produit::TYPE_QUANTITE,
            'description' => 'Câbles 2 mètres pour salles de réunion',
            'prix_indicatif' => 19.99,
            'seuil_minimum' => 10,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        $this->assertDatabaseHas('produits', [
            'nom' => 'Câble USB-C vers HDMI 2.0',
            'reference' => 'CAB-USBC-HDMI',
            'type_suivi' => Produit::TYPE_QUANTITE,
            'seuil_minimum' => 10,
        ]);

        $produit = Produit::where('reference', 'CAB-USBC-HDMI')->first();
        $response->assertRedirect(route('it.products.show', $produit));
    }

    public function test_product_reference_must_be_unique(): void
    {
        $category = Categorie::create([
            'nom' => 'Écrans',
            'icon_name' => 'monitor',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        Produit::create([
            'categorie_id' => $category->id,
            'nom' => 'Écran Dell 24',
            'reference' => 'ECR-DEL-24',
            'type_suivi' => Produit::TYPE_INDIVIDUEL,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        $response = $this->actingAs($this->itUser)->post(route('it.products.store'), [
            'nom' => 'Autre écran avec même SKU',
            'reference' => 'ECR-DEL-24',
            'categorie_id' => $category->id,
            'type_suivi' => Produit::TYPE_INDIVIDUEL,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        $response->assertSessionHasErrors('reference');
    }

    public function test_responsable_it_can_view_product_detail(): void
    {
        $category = Categorie::create([
            'nom' => 'Imprimantes',
            'icon_name' => 'printer',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        $produit = Produit::create([
            'categorie_id' => $category->id,
            'nom' => 'HP LaserJet Pro M404dn',
            'marque' => 'HP',
            'modele' => 'M404dn',
            'reference' => 'IMP-HP-M404',
            'type_suivi' => Produit::TYPE_INDIVIDUEL,
            'description' => 'Imprimante réseau monochrome',
            'caracteristiques' => 'Recto-verso automatique, Ethernet gigabit',
            'prix_indicatif' => 280.00,
            'seuil_minimum' => 1,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        $response = $this->actingAs($this->itUser)->get(route('it.products.show', $produit));

        $response->assertStatus(200);
        $response->assertSee('HP LaserJet Pro M404dn');
        $response->assertSee('IMP-HP-M404');
        $response->assertSee('280,00 €');
        $response->assertSee('Architecture découplée Catalogue & Stocks');
    }

    public function test_responsable_it_can_update_a_product(): void
    {
        $category = Categorie::create([
            'nom' => 'Ordinateurs portables',
            'icon_name' => 'laptop',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        $produit = Produit::create([
            'categorie_id' => $category->id,
            'nom' => 'ThinkPad T14 Gen 2',
            'marque' => 'Lenovo',
            'modele' => 'T14 Gen 2',
            'reference' => 'PC-LEN-T14-G2',
            'type_suivi' => Produit::TYPE_INDIVIDUEL,
            'prix_indicatif' => 1200.00,
            'seuil_minimum' => 2,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        $response = $this->actingAs($this->itUser)->put(route('it.products.update', $produit), [
            'nom' => 'ThinkPad T14 Gen 2 (Mis à jour)',
            'marque' => 'Lenovo',
            'modele' => 'T14 Gen 2',
            'reference' => 'PC-LEN-T14-G2',
            'categorie_id' => $category->id,
            'type_suivi' => Produit::TYPE_INDIVIDUEL,
            'prix_indicatif' => 1150.00,
            'seuil_minimum' => 4,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        $response->assertRedirect(route('it.products.show', $produit));
        $this->assertDatabaseHas('produits', [
            'id' => $produit->id,
            'nom' => 'ThinkPad T14 Gen 2 (Mis à jour)',
            'prix_indicatif' => 1150.00,
            'seuil_minimum' => 4,
        ]);
    }

    public function test_responsable_it_can_toggle_product_status(): void
    {
        $category = Categorie::create([
            'nom' => 'Accessoires',
            'icon_name' => 'headphones',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        $produit = Produit::create([
            'categorie_id' => $category->id,
            'nom' => 'Souris Logitech M185',
            'reference' => 'ACC-LOG-M185',
            'type_suivi' => Produit::TYPE_QUANTITE,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        $response = $this->actingAs($this->itUser)->patch(route('it.products.toggle-status', $produit));
        $response->assertRedirect();
        $this->assertEquals(Produit::STATUT_ARCHIVE, $produit->fresh()->statut);

        $response2 = $this->actingAs($this->itUser)->patch(route('it.products.toggle-status', $produit));
        $this->assertEquals(Produit::STATUT_ACTIF, $produit->fresh()->statut);
    }

    public function test_responsable_it_can_delete_a_product(): void
    {
        Storage::fake('public');

        $category = Categorie::create([
            'nom' => 'Smartphones',
            'icon_name' => 'smartphone',
            'statut' => Categorie::STATUT_ACTIF,
        ]);

        $photo = UploadedFile::fake()->image('phone.jpg');
        $photoPath = $photo->store('products', 'public');

        $produit = Produit::create([
            'categorie_id' => $category->id,
            'nom' => 'iPhone SE 2022',
            'reference' => 'TEL-IPH-SE',
            'photo' => $photoPath,
            'type_suivi' => Produit::TYPE_INDIVIDUEL,
            'statut' => Produit::STATUT_ACTIF,
        ]);

        Storage::disk('public')->assertExists($photoPath);

        $response = $this->actingAs($this->itUser)->delete(route('it.products.destroy', $produit));

        $response->assertRedirect(route('it.products.index'));
        $this->assertDatabaseMissing('produits', ['id' => $produit->id]);
        Storage::disk('public')->assertMissing($photoPath);
    }
}
