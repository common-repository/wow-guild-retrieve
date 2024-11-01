<?php
/*
Plugin Name: WoW Guild Retrieve
Plugin URI: http://www.benovermyer.com/wow-guild-retrieve
Description: Shows a roster for a World of Warcraft guild
Version: 1.3.1
Author: Ben Overmyer
Author URI: http://www.benovermyer.com/
*/

/*  Copyright 2010  Ben_Overmyer  (email : manatrance@gmail.com)

    This program is free software; you can redistribute it and/or modify
    it under the terms of the GNU General Public License, version 2, as
    published by the Free Software Foundation.

    This program is distributed in the hope that it will be useful,
    but WITHOUT ANY WARRANTY; without even the implied warranty of
    MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
    GNU General Public License for more details.

    You should have received a copy of the GNU General Public License
    along with this program; if not, write to the Free Software
    Foundation, Inc., 51 Franklin St, Fifth Floor, Boston, MA  02110-1301  USA
*/

// Return the base URL for the guild entry on the WoW Armory
function wgr_base_url( $guild, $realm, $region ) {
	$url = "";

	$guild = str_replace( ' ', '%20', $guild );
	$realm = str_replace( ' ', '%20', $realm );

	$url = "http://" . $region . ".battle.net/api/wow/guild/" . $realm . "/" . $guild . "?fields=members";

	return $url;
}

// Add sub page to the Settings Menu
function wgroptions_add_page_fn() {
	add_options_page( 'WGR Options Page', 'WoW Guild Retrieve', 'administrator', __FILE__, 'wgroptions_page_fn' );
}

function register_wgrsettings() {
	register_setting( 'wgr_rank_list', 'wgr_rank_list' );
	add_settings_section( 'wgr_main','WGR Settings','section_text_fn', __FILE__ );
	add_settings_field( 'rank_list', 'Rank List', 'ranklist_fn', __FILE__, 'wgr_main' );
}

function ranklist_fn() {
	$options = get_option( 'wgr_rank_list' );
	echo "<input id='plugin_text_string' name='wgr_rank_list[wgr_rank_list]' size='40' type='text' value='{$options[ 'wgr_rank_list' ]}' />";
}

function section_text_fn() {
?>
<p>Enter your rank names in the following text box. Remember these two rules:</p>
<ol>
	<li>Ranks are ordered by creation time in-game, and</li>
	<li>Separate each rank by a comma, with <strong>NO SPACE</strong> (e.g., Rank 1,Rank 2,Rank 3,etc.)</li>
<ol>
<?php
}

function wgroptions_page_fn() {
?>
<div class="wrap">
<h2>WoW Guild Retrieve</h2>

<form action="options.php" method="post">
<?php settings_fields( 'wgr_rank_list' ); ?>
<?php do_settings_sections( __FILE__ ); ?>
<p class="submit">
	<input name="Submit" type="submit" class="button-primary" value="<?php esc_attr_e( 'Save Changes' ); ?>" />
</p>
</form>

</div>

<?php
}

// Returns a string race name
function raceText( $num, $gen ) {
	$racename = '';
	$gendername = '';

	switch ( $num ) {
		case 1:
			$racename = 'Human';
			break;
		case 2:
			$racename = 'Orc';
			break;
		case 3:
			$racename = 'Dwarf';
			break;
		case 4:
			$racename = 'NightElf';
			break;
		case 5:
			$racename = 'Undead';
			break;
		case 6:
			$racename = 'Tauren';
			break;
		case 7:
			$racename = 'Gnome';
			break;
		case 8:
			$racename = 'Troll';
			break;
		case 9:
			$racename = 'Goblin';
			break;
		case 10:
			$racename = 'BloodElf';
			break;
		case 11:
			$racename = 'Draenei';
			break;
		case 22:
			$racename = 'Worgen';
			break;
		case 26:
			$racename = 'Pandaren';
			break;
	}

	switch ( $gen ) {
		case '0':
			$gendername = 'Male';
			break;
		case '1':
			$gendername = 'Female';
			break;
	}

	$fullrace = $racename . ' ' . $gendername;
	return $fullrace;
}

// Returns a string class name
function classText( $num ) {
	$classname = '';

	switch ( $num ) {
		case 1:
			$classname = 'Warrior';
			break;
		case 2:
			$classname = 'Paladin';
			break;
		case 3:
			$classname = 'Hunter';
			break;
		case 4:
			$classname = 'Rogue';
			break;
		case 5:
			$classname = 'Priest';
			break;
		case 6:
			$classname = 'Death Knight';
			break;
		case 7:
			$classname = 'Shaman';
			break;
		case 8:
			$classname = 'Mage';
			break;
		case 9:
			$classname = 'Warlock';
			break;
		case 10:
			$classname = 'Monk';
			break;
		case 11:
			$classname = 'Druid';
			break;
		default:
			$classname = $num;
			break;
	}

	return $classname;
}

function wgr_db_setup() {
	global $wpdb;

	$charset_collate = $wpdb->get_charset_collate();

	$table_name = $wpdb->prefix . 'wgrcache';

	$sql = "CREATE TABLE $table_name (
	id mediumint(9) NOT NULL AUTO_INCREMENT,
	cachekey varchar(255) NOT NULL,
	time TIMESTAMP DEFAULT CURRENT_TIMESTAMP NOT NULL,
	roster longtext NOT NULL,
	UNIQUE KEY id (id)
	) $charset_collate;";

	require_once( ABSPATH . 'wp-admin/includes/upgrade.php' );
	dbDelta( $sql );
}

function wgr_fetch_data( $url ) {
	global $wpdb;

	$table_name = $wpdb->prefix . 'wgrcache';

	$cache_key = md5( $url );

	$results = $wpdb->get_results( "SELECT roster FROM $table_name WHERE cachekey = '$cache_key' AND time >= NOW() - INTERVAL 10 MINUTE ORDER BY time DESC LIMIT 1", OBJECT );

	$rowCount = ( int ) $wpdb->num_rows;

	if ( $rowCount > 0 ) {
		$rosterJSON = $results[ 0 ]->roster;
	} else {
		$wpdb->query( "DELETE FROM $table_name WHERE 1" );

		$rosterJSON = file_get_contents( $url, true );

		$roster = json_decode( $rosterJSON );

		if ( !empty( $roster ) ) {
			$wpdb->insert( $table_name,
				array(
					'roster' => $rosterJSON,
					'cachekey' => $cache_key,
				)
			);
		}
	}

	return $rosterJSON;
}

function wow_guild_retrieve( $atts ) {
	extract( shortcode_atts( array(
		'guildname' => '',
		'realmname' => '',
		'region' => 'us',
		'sorttype' => '1',
		'sortorder' => 'asc',
		'restrict' => 'false',
		'tablesize' => '10'
	), $atts ) );

	$wgrpluginoptions = get_option( 'wgr_rank_list' );
	$ranklist = $wgrpluginoptions[ 'wgr_rank_list' ];
	$rankarray = explode( ',', $ranklist );

	$widgetid = 'wgrtable' . rand( 1,100 );

	$realmstr = str_replace( ' ','-',$realmname );

    $baseurl = wgr_base_url( $guildname, $realmname, $region );

	$rosterJSON = wgr_fetch_data( $baseurl );

	$roster = json_decode( $rosterJSON, true );

	$content = '';

	if ( empty( $roster ) ) {
		$content = "Unable to contact Armory.";
	} else {
		// Set up the header
		$content .= "\r\n<script type='text/javascript'>\r\n";
		$content .= "jQuery(document).ready(function($) {\r\n";
        $content .= "\t$('#" . $widgetid . "').dataTable({'iDisplayLength':" . $tablesize . "}).fnSort([[" . $sorttype . ",'" . $sortorder . "']]);\r\n";
        $content .= "\tvar widgetLoad = { site: document.URL, widget: '$widgetid' };";
		$content .= "});\r\n";
		$content .= "</script>\r\n\r\n";
		$content .= "<div id='guild-data-div'>\r\n";
		$content .= "<div id='header-block'>\r\n";

		// Process roster data
		$gname = $roster[ 'name' ];
		$gfaction = $roster[ 'side' ];
		$glevel = $roster[ 'level' ];
		$gachieve = $roster[ 'achievementPoints' ];

		// Output the roster table header
		$content .= '<img src="' . WP_PLUGIN_URL . '/wow-guild-retrieve/images/faction-' . $gfaction . '.png" alt="" height="64" width="64"/>';
		$content .= '<div id="header-text">';
		$content .= '<h2>' . $gname . '</h2>';
		$content .= '<h3>' . $gachieve . ' Achievement Points | Level ' . $glevel . '</h3>';
		$content .= '</div>';
		$content .= '<div class="clear"></div>';
		$content .= '</div>';
		$content .= "<table id='$widgetid' class='dataTable'>\n";
		$content .= "<thead><tr><th class='ginfo-name'>Name</th><th class='ginfo-race'>Race</th><th class='ginfo-class'>Class</th><th class='ginfo-level'>Level</th><th class='ginfo-rank'>Rank</th><th class='ginfo-achieve'>Achievement Points</th></tr></thead>\n";
		$content .= "<tbody>\n";

		$whichrow = 0;

		// Output a member row
		foreach( $roster[ 'members' ] as $character ) {
			$racenum = $character[ 'character' ][ 'race' ];
			$gennum = $character[ 'character' ][ 'gender' ];
			$race = raceText( $racenum,$gennum );
			$raceimg = 'race-' . str_replace( ' ', '-', $race ) . '.png';

			$classnum = $character[ 'character' ][ 'class' ];
			$class = classText( $classnum );
			$classimg = 'class-' . str_replace( ' ', '', $class ) . '.png';

			$achieve = $character[ 'character' ][ 'achievementPoints' ];

			$rankid = $character[ 'rank' ];

			if ( empty( $rankarray[ $rankid ] ) ) {
				$rank = "Rank " . $rankid;
			}else{
				$rank = $rankarray[ $rankid ];
			}
			$ranknum = str_replace( 'Rank ', '', $rankid );

			$level = $character[ 'character' ][ 'level' ];

			$cname = $character[ 'character' ][ 'name' ];

			if ( ( $restrict == 'true' && $level == '90' ) OR ( $restrict == 'false' ) ) {
				if ( $whichrow == 0 ) {
					$rowstyle = "odd";
				} else {
					$rowstyle = "even";
				}
				$content .= "<tr class='$rowstyle'>";
				$content .= "<td class='ginfo-name'><a href='http://" . $region . ".battle.net/wow/en/character/" . $realmstr . "/" . $cname . "/simple'>" . $cname . "</a></td><td class='ginfo-race'><img src='" . WP_PLUGIN_URL . "/wow-guild-retrieve/images/" . $raceimg . "' alt='" . $race . "' width='32' height='32'/></td><td class='ginfo-class'><img src='" . WP_PLUGIN_URL . "/wow-guild-retrieve/images/" . $classimg . "' alt='" . $class . "' width='32' height='32'/></td><td class='ginfo-level'>" . $level . "</td><td class='ginfo-rank'>" . $ranknum . "| " . $rank . "</td><td class='ginfo-achieve'>" . $achieve . "</td></tr>";
				$content .= "</tr>\n";
				$whichrow++;
				if ( $whichrow >= 2 ) {
					$whichrow = 0;
				}
			}
		}
		$content .= "</tbody>";
		$content .= "</table>";
		$content .= "<div class='clear'></div>";
		$content .= "</div>";
	}

	return $content;
}

function wgr_scripts() {
	wp_enqueue_script( 'jquery' );
	wp_enqueue_script( 'wgrdatascript', '/wp-content/plugins/wow-guild-retrieve/js/jquery.dataTables.min.js', array( 'jquery' ) );
}

function wgr_styles() {
	wp_enqueue_style( 'wgrstyle', '/wp-content/plugins/wow-guild-retrieve/css/wgr.css' );
}

register_activation_hook( __FILE__, 'wgr_db_setup' );
add_action( 'wp_enqueue_scripts', 'wgr_scripts' );
add_action( 'wp_enqueue_scripts', 'wgr_styles' );
add_action( 'admin_init', 'register_wgrsettings' );
add_action( 'admin_menu', 'wgroptions_add_page_fn' );
add_shortcode( 'wgr','wow_guild_retrieve' );
