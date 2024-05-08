<?php 

function download_csv_file()
{

	function upload_image_from_url_to_post($image_url, $post_id)
	{
		// Check if the image already exists in the media library.
		$image_name = basename($image_url);
		$existing_attachment = get_page_by_title($image_name, 'OBJECT', 'attachment');

		// If the image already exists, return its ID.
		if ($existing_attachment) {
			return $existing_attachment->ID;
		}

		// If the image doesn't exist, download and upload it to the media library.
		$image_data = file_get_contents($image_url);
		if ($image_data) {
			$upload_dir  = wp_upload_dir();
			$upload_path = $upload_dir['path'] . '/' . $image_name;
			$upload_file = file_put_contents($upload_path, $image_data);

			// Check if the image was successfully uploaded.
			if ($upload_file) {
				$wp_filetype = wp_check_filetype($upload_path, null);
				$attachment = array(
					'post_mime_type' 	=> $wp_filetype['type'],
					'post_title' 		=> sanitize_file_name($image_name),
					'post_content' 		=> '',
					'post_status' 		=> 'inherit'
				);
				$attachment_id = wp_insert_attachment($attachment, $upload_path, $post_id);
				if (!is_wp_error($attachment_id)) {
					require_once(ABSPATH . 'wp-admin/includes/image.php');
					$attachment_data = wp_generate_attachment_metadata($attachment_id, $upload_path);
					wp_update_attachment_metadata($attachment_id, $attachment_data);
					return $attachment_id;
				}
			}
		}

		return false;
	}

	function upload_images_to_posts($image_urls, $post_ids)
	{
		foreach ($post_ids as $post_id) {
			$gallery_ids = array();
			foreach ($image_urls as $image_url) {
				$attachment_id = upload_image_from_url_to_post($image_url, $post_id);
				if ($attachment_id) {
					$gallery_ids[] = $attachment_id;
				} else {
					// echo "Failed to upload image from URL $image_url to post ID $post_id.\n";
				}
			}
			// Update post meta with the array of attachment IDs for the gallery
			update_post_meta($post_id, 'aptsm_gallery', $gallery_ids);
		}
	}

	// fetch csv data processing 
	function fetch_csv_data($url)
	{
		$csvData = [];
		if (($handle = fopen($url, 'r')) !== false) {
			while (($row = fgetcsv($handle)) !== false) {
				$csvData[] = $row;
			}
			fclose($handle);
		}
		return $csvData;
	}

	// Call the function to fetch CSV data
	$csv_url = 'https://rodarealestatecomo.com/csvinfo/test-csv-pro.csv';
	$csv_data = fetch_csv_data($csv_url);
	// Output the CSV data
	foreach ($csv_data as $single_data) {
		$pro_title = $single_data[0] ?? '';
		// if ($pro_title != '') {
		$product_gall = [];
		$product_gall[] = 'https://rodarealestatecomo.com/csvinfo/images/' . $single_data[15] ?? '';
		$product_gall[] = 'https://rodarealestatecomo.com/csvinfo/images/' . $single_data[16] ?? '';
		$product_gall[] = 'https://rodarealestatecomo.com/csvinfo/images/' . $single_data[17] ?? '';
		$product_gall[] = 'https://rodarealestatecomo.com/csvinfo/images/' . $single_data[18] ?? '';
		$product_gall[] = 'https://rodarealestatecomo.com/csvinfo/images/' . $single_data[19] ?? '';
		$product_gall[] = 'https://rodarealestatecomo.com/csvinfo/images/' . $single_data[20] ?? '';
		$product_gall[] = 'https://rodarealestatecomo.com/csvinfo/images/' . $single_data[21] ?? '';
		$product_gall[] = 'https://rodarealestatecomo.com/csvinfo/images/' . $single_data[22] ?? '';
		$product_gall[] = 'https://rodarealestatecomo.com/csvinfo/images/' . $single_data[23] ?? '';
		$product_gall[] = 'https://rodarealestatecomo.com/csvinfo/images/' . $single_data[24] ?? '';
		$product_gall[] = 'https://rodarealestatecomo.com/csvinfo/images/' . $single_data[25] ?? '';
		$product_gall[] = 'https://rodarealestatecomo.com/csvinfo/images/' . $single_data[26] ?? '';
		$product_gall[] = 'https://rodarealestatecomo.com/csvinfo/images/' . $single_data[27] ?? '';
		$product_gall[] = 'https://rodarealestatecomo.com/csvinfo/images/' . $single_data[28] ?? '';


		$title_to_check = $pro_title;
		$args = array(
			'post_type' => 'apartment',
			'post_status' => 'publish',
			'posts_per_page' => 1,
			'title' => $title_to_check
		);

		$query = new WP_Query($args);

		if ($query->have_posts()) {
			while ($query->have_posts()) {
				$query->the_post();
				$post_ID = get_the_ID();

				$post_ids = array($post_ID);
				upload_images_to_posts($product_gall, $post_ids);
			}
		} else {

			$new_apart = array(
				'post_title'    => $title_to_check,
				'post_status'   => 'publish',
				'post_type'     => 'apartment',
			);

			$post_id = wp_insert_post($new_apart);
			$post_ids = array($post_id);
			upload_images_to_posts($product_gall, $post_ids);
		}

		wp_reset_query();
		// }
	}
}

download_csv_file();

?>
