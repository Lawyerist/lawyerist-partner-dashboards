<?php

// Register Custom Post Type
function partner_post_type() {

	$labels = array(
		'name'                  => 'Partners',
		'singular_name'         => 'Partner',
		'menu_name'             => 'Partners',
		'name_admin_bar'        => 'Partners',
		'archives'              => 'Partners',
		'attributes'            => 'Partner Attributes',
		'parent_item_colon'     => 'Parent Item:',
		'all_items'             => 'All Partners',
		'add_new_item'          => 'Add New Partner',
		'add_new'               => 'Add New',
		'new_item'              => 'New Partner',
		'edit_item'             => 'Edit Partner',
		'update_item'           => 'Update Partner',
		'view_item'             => 'View Partner',
		'view_items'            => 'View Partners',
		'search_items'          => 'Search Partners',
		'not_found'             => 'Partner not found',
		'not_found_in_trash'    => 'Partner not found in Trash',
		'featured_image'        => 'Partner Logo',
		'set_featured_image'    => 'Set partner logo',
		'remove_featured_image' => 'Remove partner logo',
		'use_featured_image'    => 'Use as partner logo',
		'insert_into_item'      => 'Insert into partner',
		'uploaded_to_this_item' => 'Uploaded to this partner',
		'items_list'            => 'List of partners',
		'items_list_navigation' => 'Partner list navigation',
		'filter_items_list'     => 'Filter list of partners',
	);

	$args = array(
		'label'                 => 'Partner',
		'description'           => 'Lawyerist\'s advertising partners.',
		'labels'                => $labels,
		'supports'              => array( 'title', 'thumbnail' ),
		'hierarchical'          => false,
		'public'                => true,
		'show_ui'               => true,
		'show_in_menu'          => true,
		'menu_position'         => 20,
		'menu_icon'             => 'dashicons-groups',
		'show_in_admin_bar'     => true,
		'show_in_nav_menus'     => false,
		'can_export'            => true,
		'has_archive'           => false,
		'exclude_from_search'   => true,
		'publicly_queryable'    => true,
		'capability_type'       => 'page',
		'show_in_rest'          => false,
	);

	register_post_type( 'partner', $args );

}

add_action( 'init', 'partner_post_type', 0 );
