<?php
/**
 * As configurações básicas do WordPress
 *
 * O script de criação wp-config.php usa esse arquivo durante a instalação.
 * Você não precisa usar o site, você pode copiar este arquivo
 * para "wp-config.php" e preencher os valores.
 *
 * Este arquivo contém as seguintes configurações:
 *
 * * Configurações do MySQL
 * * Chaves secretas
 * * Prefixo do banco de dados
 * * ABSPATH
 *
 * @link https://wordpress.org/support/article/editing-wp-config-php/
 *
 * @package WordPress
 */

// ** Configurações do MySQL - Você pode pegar estas informações com o serviço de hospedagem ** //
/** O nome do banco de dados do WordPress */
define( 'DB_NAME', 'sinop' );

/** Usuário do banco de dados MySQL */
define( 'DB_USER', 'root' );

/** Senha do banco de dados MySQL */
define( 'DB_PASSWORD', '' );

/** Nome do host do MySQL */
define( 'DB_HOST', 'localhost' );

/** Charset do banco de dados a ser usado na criação das tabelas. */
define( 'DB_CHARSET', 'utf8mb4' );

/** O tipo de Collate do banco de dados. Não altere isso se tiver dúvidas. */
define( 'DB_COLLATE', '' );

/**#@+
 * Chaves únicas de autenticação e salts.
 *
 * Altere cada chave para um frase única!
 * Você pode gerá-las
 * usando o {@link https://api.wordpress.org/secret-key/1.1/salt/ WordPress.org
 * secret-key service}
 * Você pode alterá-las a qualquer momento para invalidar quaisquer
 * cookies existentes. Isto irá forçar todos os
 * usuários a fazerem login novamente.
 *
 * @since 2.6.0
 */
define( 'AUTH_KEY',         'nBhNl7u*#[T(h]yq1aR[d-mORZYYl2J-0ID3JPy$u@|.pvN9Xmw+Yx{mb:PDJ,5w' );
define( 'SECURE_AUTH_KEY',  'd ppTkuq!4>]xiw1s!r@!d%cu)#!P!^g9SUNnT2?|uk$h(<BvLy UWiSf}QS9lO)' );
define( 'LOGGED_IN_KEY',    'vXaZfzygO1u3iePu.ei/hAwCeqG6G,WB:4{_HRRsy$+=b%aA/}J|0irOBl)IIDX!' );
define( 'NONCE_KEY',        'X/zGaxwU{HHt7DdmS`x`oYKLNf%AxFHp6UKp?/O:u}IMDb-]^Tm`vgju*@:3ND@d' );
define( 'AUTH_SALT',        'M<1Jp?~Su{CKzxn&b&9@]~q;CIFx0X|IX,C5Y`zl/c@Tk&*S%RK-FmPu6U$;*/f}' );
define( 'SECURE_AUTH_SALT', 'n*bIZ79GeJU#%B3YX,z<!64z8S/D|P:gfYN{NesEjh~|G75JpmJW]yl@eBipVY7&' );
define( 'LOGGED_IN_SALT',   'yf(:l6q2SIZd+lS)H7jUN;}|i/T}09zt%oX&/p(CeFZRiD*|}%}YPRLKP]TSQL|a' );
define( 'NONCE_SALT',       '}PdIAfbAI=PCLlVO%^QXQ,Bv]MH;,V:SatMD1` sDaTVAor??hFW+^d5bx}M1(D,' );

/**#@-*/

/**
 * Prefixo da tabela do banco de dados do WordPress.
 *
 * Você pode ter várias instalações em um único banco de dados se você der
 * um prefixo único para cada um. Somente números, letras e sublinhados!
 */
$table_prefix = 'wp_';

/**
 * Para desenvolvedores: Modo de debug do WordPress.
 *
 * Altere isto para true para ativar a exibição de avisos
 * durante o desenvolvimento. É altamente recomendável que os
 * desenvolvedores de plugins e temas usem o WP_DEBUG
 * em seus ambientes de desenvolvimento.
 *
 * Para informações sobre outras constantes que podem ser utilizadas
 * para depuração, visite o Codex.
 *
 * @link https://wordpress.org/support/article/debugging-in-wordpress/
 */
define( 'WP_DEBUG', false );

/* Adicione valores personalizados entre esta linha até "Isto é tudo". */



/* Isto é tudo, pode parar de editar! :) */

/** Caminho absoluto para o diretório WordPress. */
if ( ! defined( 'ABSPATH' ) ) {
	define( 'ABSPATH', __DIR__ . '/' );
}

/** Configura as variáveis e arquivos do WordPress. */
require_once ABSPATH . 'wp-settings.php';
