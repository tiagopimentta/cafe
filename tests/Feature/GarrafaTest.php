<?php

namespace Tests\Feature;

use App\Consumo;
use App\User;
use Illuminate\Foundation\Testing\RefreshDatabase;
use Illuminate\Support\Str;
use Tests\TestCase;

class GarrafaTest extends TestCase
{
    use RefreshDatabase;

    public $admin;

    public function setUp(): void
    {
        parent::setUp();

        $this->admin = User::create([
            'name'  => 'Admin',
            'email' => 'admin@cafe.com',
            'email_verified_at' => now(),
            'password' => '$2y$10$92IXUNpkjO0rOQ5byMi.Ye4oKoEa3Ro9llC/.og/at2.uheWG/igi', // password
            'remember_token' => Str::random(10),
            'admin' => true,
        ]);

        $this->admin->garrafa()->create([
            'capacidade_total'  => 1000,
            'quantidade_atual'  => 0,
            'capacidade_xicara' => 200,
            'limite_cafe' => 3,
        ]);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_o_usuario_nao_admin_nao_pode_acessar_a_rota_de_relatorio_de_consumo()
    {
        // Arrange
        $userNaoAdmin = factory(User::class)->create();

        // Act
        $response = $this->actingAs($userNaoAdmin)
            ->get('/consumo');

        // Assert
        $response->assertRedirect('/home');
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_quando_o_admin_enche_a_garrafa_aumenta_a_mesma_quantidade_informada_de_cafes()
    {
        // Arrange
        $xicarasParaEncher = random_int(1, 5);

        // Act
        $response = $this->actingAs($this->admin)
            ->post('/encher', ['xicaras' => $xicarasParaEncher]);

        // Assert
        $response->assertRedirect('/home');

        $garrafa = $this->admin->garrafa()->first();
        $quantidadeAtual  = $garrafa->quantidade_atual;
        $capacidadeXicara = $garrafa->capacidade_xicara;

        $this->assertEquals($quantidadeAtual, $xicarasParaEncher * $capacidadeXicara);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_quando_o_admin_deve_informar_a_quantidade_de_cafe_para_colocar_na_garrafa()
    {
        // Arrange

        // Act
        $response = $this->actingAs($this->admin)
            ->post('/encher');

        // Assert
        $response->assertRedirect('/home');

        $response->assertSessionHas('warning', 'Você deve informar a quantidade de café!');
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_quando_o_admin_deve_informar_no_minimo_uma_xicara_de_cafe_para_colocar_na_garrafa()
    {
        // Arrange
        $xicarasParaEncher = 0;

        // Act
        $response = $this->actingAs($this->admin)
            ->post('/encher', ['xicaras' => $xicarasParaEncher]);

        // Assert
        $response->assertRedirect('/home');

        $response->assertSessionHas('warning', 'Você deve informar a quantidade de café!');
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_quando_o_admin_enche_a_garrafa_com_quantidade_maior_que_o_limite_a_garrafa_fica_totalmente_cheia_e_exibe_mensagem_da_quantidade_que_derramou()
    {
        // Arrange
        $xicarasParaEncher = random_int(6, 10);

        $garrafa = $this->admin->garrafa()->first();
        $quantidadeAtual   = $garrafa->quantidade_atual;
        $capacidadeGarrafa = $garrafa->capacidade_total;
        $capacidadeXicara  = $garrafa->capacidade_xicara;

        $quantidadeMlQueVaiDerramar = ($xicarasParaEncher * $capacidadeXicara) - $capacidadeGarrafa;
        $xicarasQueIraoDerramar = $quantidadeMlQueVaiDerramar / $capacidadeXicara;

        // Act
        $response = $this->actingAs($this->admin)
            ->post('/encher', ['xicaras' => $xicarasParaEncher]);

        // Assert
        $response->assertRedirect('/home');

        $response->assertSessionHas('error', 'Você colocou mais xícaras de café do que cabem na garrafa! ' .
        "Você derramou $xicarasQueIraoDerramar xícaras de café ($quantidadeMlQueVaiDerramar ml)");

        $garrafa = $this->admin->garrafa()->first();
        $quantidadeAtual   = $garrafa->quantidade_atual;
        $capacidadeGarrafa = $garrafa->capacidade_total;

        $this->assertEquals($quantidadeAtual, $capacidadeGarrafa);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_quando_um_usuario_bebe_a_quantidade_de_cafes_da_garrafa_diminui_1()
    {
        // Arrange
        $xicaras = random_int(1, 5);

        $garrafa = $this->admin->garrafa()->first();
        $capacidadeXicara  = $garrafa->capacidade_xicara;
        $quantidadeInicial = $xicaras * $capacidadeXicara;

        $garrafa->update([
            'quantidade_atual' => $quantidadeInicial,
        ]);

        // Act
        $response = $this->actingAs($this->admin)
            ->get('/beber');

        // Assert
        $response->assertRedirect('home');

        $garrafa             = $this->admin->garrafa()->first();
        $quantidadeAposBeber = $garrafa->quantidade_atual;


        $this->assertEquals($quantidadeInicial - $capacidadeXicara, $quantidadeAposBeber);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_quando_um_usuario_bebe_registra_no_relatorio()
    {
        // Arrange
        $consumosAntesDeBeber = Consumo::all()->count();

        // Act
        $response = $this->actingAs($this->admin)
            ->get('/beber');

        // Assert
        $response->assertRedirect('home');

        $consumosAposBeber = Consumo::all()->count();

        $this->assertEquals($consumosAposBeber, $consumosAntesDeBeber + 1);
    }

    /**
     * A basic unit test example.
     *
     * @return void
     */
    public function test_quando_um_usuario_bebe_muito_cafe_exibe_mensagem_reclamando()
    {
        // Arrange
        $garrafa = $this->admin->garrafa()->first();
        $limiteCafe = $garrafa->limite_cafe;

        $consumosAntesDeBeber = Consumo::all()->count();

        // Act
        for($cafe = 0; $cafe <= $limiteCafe; $cafe++) {
            $response = $this->actingAs($this->admin)
                ->get('/beber');
        }

        // Assert
        $response->assertRedirect('home');

        $response->assertSessionHas('warning', 'Você está bebendo muito café!');
    }
}
