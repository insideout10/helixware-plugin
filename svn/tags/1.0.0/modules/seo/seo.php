<?php

/**
 * Provides Video SEO related functions.
 *
 * @since 4.0.0
 */


/**
 * This method hooks up to the <strong>hewa_player_start_element</strong> filter in order to add SEO attributes to the
 * tag.
 *
 * @since 4.0.0
 *
 * @param $asset_id number The asset id.
 *
 * @return string The string content to append to the video opening tag.
 */
function hewa_seo_player_start_element( $asset_id ) {

	return ' itemprop="video" itemscope="" itemtype="http://schema.org/VideoObject"';
}

add_filter( 'hewa_player_start_element', 'hewa_seo_player_start_element', 10 );

/**
 * This method hooks up to the <strong>hewa_player_in_element</strong> filter in order to add SEO attributes inside the
 * video tag.
 *
 * @since 4.0.0
 *
 * @param  $asset_id number The asset id.
 *
 * @return string The string content to output inside the video tag.
 */

function hewa_seo_player_in_element( $asset_id ) {

	// Get the asset details in order to print some SEO tags.
	$asset   = hewa_get_asset( $asset_id );
	$title_h = esc_html( $asset->title );
	$title_j = json_encode( $asset->title );

	$description_h = esc_html( $asset->description );
	$description_j = json_encode( $asset->description );

	$duration_h = esc_html( gmdate( 'H:i:s', $asset->duration ) );
	$duration_j = json_encode( gmdate( 'H:i:s', $asset->duration ) );

	$thumbnail_url   = hewa_get_server_url() . "/4/pub/asset/$asset_id/image?w=640&tc=00:00:03";
	$thumbnail_url_j = json_encode( $thumbnail_url );

	$content_url   = get_permalink();
	$content_url_j = json_encode( $content_url );

	// Print the standard player DIV.
	return <<<EOF
		<h2 itemprop="name">$title_h</h2>
		<meta itemprop="duration" content="$duration_h" />
		<meta itemprop="thumbnailUrl" content="$thumbnail_url" />
		<meta itemprop="contentUrl" content="$content_url" />
		<meta itemprop="uploadDate" content="2014-01-03T08:00:00+08:00" />
		<span itemprop="description">$description_h</span>
		<script type="application/ld+json">
			{
				"@context": "http://schema.org",
				"@type": "VideoObject",
				"name": $title_j,
				"description": $description_j,
				"duration": $duration_j,
				"contentUrl": $content_url_j,
				"url": $content_url_j,
				"thumbnail": {
					"@type": "ImageObject",
					"contentUrl": $thumbnail_url_j
				}
			}
		</script>
EOF;

}

add_filter( 'hewa_player_in_element', 'hewa_seo_player_in_element', 10 );