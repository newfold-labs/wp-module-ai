<?php
namespace NewfoldLabs\WP\Module\AI;

/**
 * Class Patterns
 */
final class Patterns {

	/**
	 * Retrieve custom content structure.
	 *
	 * @return array
	 */
	public static function get_custom_content_structure() {
		return array( 'header', 'hero-custom', 'footer' );
	}

	/**
	 * Check whether custom hero to be used or not.
	 *
	 * @param array $site_classification site classification as determined by AI
	 * @return array|boolean
	 */
	public static function check_custom_content_structure_needed( $site_classification ) {
		$primary_sitetype               = $site_classification['primaryType'];
		$secondary_sitetype             = $site_classification['slug'];
		$sitetypes_for_simpler_homepage = self::get_sitetypes_for_simpler_homepage();

		if ( isset( $sitetypes_for_simpler_homepage[ $primary_sitetype ][ $secondary_sitetype ] ) ) {
			return $sitetypes_for_simpler_homepage[ $primary_sitetype ][ $secondary_sitetype ];
		}
		return false;
	}

	/**
	 * List of primary and secondary sitetype.
	 *
	 * @return array
	 */
	public static function get_sitetypes_for_simpler_homepage() {
		$sitetypes_for_simpler_homepage = array(
			'business'            => array(
				'agency-consulting'      => false,
				'autos-repair'           => false,
				'arts-crafts'            => false,
				'child-care'             => false,
				'events'                 => false,
				'finance'                => false,
				'garden-florist'         => false,
				'insurance'              => false,
				'trades-repair-services' => false,
				'hr-recruiting'          => false,
				'legal'                  => false,
				'marketing'              => false,
				'pr-communications'      => false,
				'real-estate-management' => false,
				'outdoors'               => false,
				'shopping-retail'        => false,
				'weddings'               => false,
				'other'                  => false,
			),
			'creative'            => array(
				'artist'          => true,
				'cosplay'         => true,
				'digital-creator' => true,
				'influencer'      => true,
				'model'           => true,
				'photogrpahy'     => true,
				'writing'         => false,
				'other'           => true,
			),
			'education'           => array(
				'after-school'         => false,
				'driving-schools'      => false,
				'online-courses'       => false,
				'schools-universities' => false,
				'student-organization' => false,
				'teacher'              => true,
				'tutoring'             => false,
				'test-preparation'     => false,
				'other'                => false,
			),
			'entertainment'       => array(
				'comedy'            => false,
				'dance-theater'     => false,
				'film-tv'           => false,
				'gaming-e-sports'   => false,
				'video-streaming'   => false,
				'live-events'       => false,
				'music'             => false,
				'publishing-media'  => false,
				'radio-podcasts'    => true,
				'talent-management' => false,
				'other'             => true,
			),
			'government-politics' => array(
				'activism-advocacy'    => false,
				'emergency-relief'     => false,
				'judiciary'            => false,
				'law-enforcement'      => false,
				'libraries'            => false,
				'military-veterans'    => false,
				'policy-campaigns'     => false,
				'politicians'          => false,
				'public-services'      => false,
				'towns-cities-regions' => false,
				'other'                => false,
			),
			'food-beverage'       => array(
				'bars'            => false,
				'bakeries'        => false,
				'catering'        => false,
				'chefs'           => false,
				'coffee-tea'      => false,
				'farms'           => false,
				'food-trucks'     => false,
				'grocers-markets' => false,
				'recipes'         => false,
				'other'           => false,
			),
			'fashion-beauty'      => array(
				'accessories'       => false,
				'clothing'          => false,
				'fragrances'        => false,
				'haircare'          => false,
				'jewelry'           => false,
				'makeup-skincare'   => false,
				'nailcare'          => false,
				'shoes'             => false,
				'stylists'          => true,
				'tattoos-piercings' => false,
				'other'             => false,
			),
			'health-wellness'     => array(
				'counseling-mental-health' => false,
				'dentist-ortho'            => false,
				'doctor'                   => false,
				'gym'                      => false,
				'trainer'                  => true,
				'nutrition-weight-loss'    => true,
				'physical-therapist'       => true,
				'retreats'                 => false,
				'spas'                     => false,
				'spirituality'             => false,
				'other'                    => false,
			),
			'nonprofit'           => array(
				'animals-wildlife'           => false,
				'climate-environment'        => false,
				'civic-community-groups'     => false,
				'diversity-equity-inclusion' => false,
				'foundations'                => false,
				'military-veterans'          => false,
				'museums'                    => false,
				'religious-groups'           => false,
				'trade-professional-groups'  => false,
			),
			'personal'            => array(
				'art'                => true,
				'blog'               => false,
				'creative-portfolio' => true,
				'digital-creator'    => true,
				'influencer'         => true,
				'model'              => true,
				'photography'        => true,
				'writing'            => false,
				'wedding'            => false,
				'other'              => true,
			),
			'tech'                => array(
				'agency-consulting'  => false,
				'apps-software'      => false,
				'blockchain'         => false,
				'edutech'            => false,
				'fintech'            => false,
				'hardware-wearables' => false,
				'social-communities' => false,
				'services-saas'      => false,
				'other'              => false,
			),
			'travel-tourism'      => array(
				'attractions'         => false,
				'hotels-lodging'      => false,
				'property-management' => false,
				'tours-guides'        => false,
				'rentals'             => false,
				'travel-agency'       => false,
				'travel-influencer'   => false,
				'other'               => false,
			),
		);

		return $sitetypes_for_simpler_homepage;
	}

	/**
	 * Retrieve custom pattern.
	 *
	 * @return array
	 */
	public static function get_custom_hero_pattern() {
		return array(
			'replacedPattern' => "<!-- wp:group {\"metadata\":{\"name\":\"Hero\"},\"className\":\"nfd-container nfd-p-lg\",\"layout\":{\"type\":\"constrained\"},\"nfdGroupTheme\":\"white\"} -->\n<div class=\"nfd-container nfd-p-lg wp-block-group nfd-bg-surface nfd-theme-white\"><!-- wp:columns {\"className\":\"nfd-gap-4xl nfd-gap-y-3xl\"} -->\n<div class=\"nfd-gap-4xl nfd-gap-y-3xl wp-block-columns\"><!-- wp:column {\"verticalAlignment\":\"center\",\"width\":\"\"} -->\n<div class=\"wp-block-column is-vertically-aligned-center\"><!-- wp:group {\"className\":\"nfd-gap-lg\",\"layout\":{\"type\":\"flex\",\"orientation\":\"vertical\"}} -->\n<div class=\"nfd-gap-lg wp-block-group\"><!-- wp:heading {\"textAlign\":\"left\",\"level\":1,\"className\":\"nfd-text-huge nfd-text-contrast nfd-text-balance\"} -->\n<h1 class=\"nfd-text-huge nfd-text-contrast nfd-text-balance wp-block-heading has-text-align-left\">Hello! I'm Renée Laughton.</h1>\n<!-- /wp:heading -->\n\n<!-- wp:paragraph {\"align\":\"left\",\"className\":\"nfd-text-md nfd-text-faded nfd-text-balance\"} -->\n<p class=\"nfd-text-md nfd-text-faded nfd-text-balance has-text-align-left\">Come along for the journey with me, my husband, our two kids and Shiba Inu as we share the best Southern California has to offer!</p>\n<!-- /wp:paragraph -->\n\n<!-- wp:spacer {\"height\":\"0px\",\"style\":{\"layout\":{\"flexSize\":\"0px\",\"selfStretch\":\"fixed\"}}} -->\n<div style=\"height:0px\" aria-hidden=\"true\" class=\"wp-block-spacer\"></div>\n<!-- /wp:spacer --></div>\n<!-- /wp:group -->\n\n<!-- wp:social-links -->\n<ul class=\"wp-block-social-links\"><!-- wp:social-link {\"url\":\"#\",\"service\":\"facebook\"} /-->\n\n<!-- wp:social-link {\"url\":\"#\",\"service\":\"instagram\"} /-->\n\n<!-- wp:social-link {\"url\":\"#\",\"service\":\"tiktok\"} /-->\n\n<!-- wp:social-link {\"url\":\"#\",\"service\":\"youtube\"} /-->\n\n<!-- wp:social-link {\"url\":\"#\",\"service\":\"snapchat\"} /--></ul>\n<!-- /wp:social-links -->\n\n<!-- wp:spacer {\"height\":\"6px\",\"style\":{\"layout\":{}}} -->\n<div style=\"height:6px\" aria-hidden=\"true\" class=\"wp-block-spacer\"></div>\n<!-- /wp:spacer -->\n\n<!-- wp:buttons -->\n<div class=\"wp-block-buttons\"><!-- wp:button {\"width\":50} -->\n<div class=\"wp-block-button has-custom-width wp-block-button__width-50\"><a class=\"wp-block-button__link wp-element-button\">About me</a></div>\n<!-- /wp:button -->\n\n<!-- wp:button {\"width\":50,\"className\":\"is-style-outline\"} -->\n<div class=\"is-style-outline wp-block-button has-custom-width wp-block-button__width-50\"><a class=\"wp-block-button__link wp-element-button\">Contact me</a></div>\n<!-- /wp:button --></div>\n<!-- /wp:buttons --></div>\n<!-- /wp:column -->\n\n<!-- wp:column {\"verticalAlignment\":\"center\",\"width\":\"\"} -->\n<div class=\"wp-block-column is-vertically-aligned-center\"><!-- wp:image {\"aspectRatio\":\"1\",\"scale\":\"cover\",\"sizeSlug\":\"large\",\"className\":\"nfd-rounded-lg\"} -->\n<figure class=\"nfd-rounded-lg wp-block-image size-large\"><img src=\"https://images.unsplash.com/photo-1665686304355-0b09b1e3b03c?ixlib=rb-4.0.3&amp;ixid=MnwxMjA3fDB8MHxwaG90by1wYWdlfHx8fGVufDB8fHx8&amp;auto=format&amp;fit=crop&amp;q=80&amp;w=800&amp;h=800&amp;crop=\" alt=\"\" style=\"aspect-ratio:1;object-fit:cover\"/></figure>\n<!-- /wp:image --></div>\n<!-- /wp:column --></div>\n<!-- /wp:columns --></div>\n<!-- /wp:group -->",
			'dalleImages'     => array(),
		);
	}
}
