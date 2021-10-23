<?php
$wp_customize->add_setting( 'm_shop_disable_product_img_sec', array(
                'default'               => false,
                'sanitize_callback'     => 'm_shop_sanitize_checkbox',
            ) );
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'm_shop_disable_product_img_sec', array(
                'label'                 => esc_html__('Disable Section', 'm-shop'),
                'type'                  => 'checkbox',
                'section'               => 'm_shop_product_tab_image',
                'settings'              => 'm_shop_disable_product_img_sec',
 ) ) );

// section heading
$wp_customize->add_setting('m_shop_product_img_sec_heading', array(
        'default' => __('Product Tab Image Carousel','m-shop'),
        'capability'        => 'edit_theme_options',
        'sanitize_callback' => 'm_shop_sanitize_text',
        'transport'         => 'postMessage',
));
$wp_customize->add_control( 'm_shop_product_img_sec_heading', array(
        'label'    => __('Section Heading', 'm-shop'),
        'section'  => 'm_shop_product_tab_image',
         'type'       => 'text',
));

//= Choose All Category  =   
    if (class_exists( 'M_Shop_Customize_Control_Checkbox_Multiple')){
   $wp_customize->add_setting('m_shop_product_img_sec_cat_list', array(
        'default'           => '',
        'sanitize_callback' => 'm_shop_checkbox_explode'
    ));
    $wp_customize->add_control(new M_Shop_Customize_Control_Checkbox_Multiple(
            $wp_customize,'m_shop_product_img_sec_cat_list', array(
        'settings'=> 'm_shop_product_img_sec_cat_list',
        'label'   => __( 'Choose Categories To Show', 'm-shop' ),
        'section' => 'm_shop_product_tab_image',
        'choices' => m_shop_get_category_list(array('taxonomy' =>'product_cat'),true),
        ) 
    ));

}  

$wp_customize->add_setting('m_shop_product_img_sec_optn', array(
        'default'        => 'recent',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'm_shop_sanitize_select',
    ));
$wp_customize->add_control( 'm_shop_product_img_sec_optn', array(
        'settings' => 'm_shop_product_img_sec_optn',
        'label'   => __('Choose Option','open-mart'),
        'section' => 'm_shop_product_tab_image',
        'type'    => 'select',
        'choices'    => array(
        'recent'     => __('Recent','m-shop'),
        'featured'   => __('Featured','m-shop'),
        'random'     => __('Random','m-shop'),
            
        ),
    ));

// Add an option to disable the logo.
  $wp_customize->add_setting( 'm_shop_product_img_sec_slider_optn', array(
    'default'           => false,
    'sanitize_callback' => 'm_shop_sanitize_checkbox',
  ) );
  $wp_customize->add_control( new m_shop_Toggle_Control( $wp_customize, 'm_shop_product_img_sec_slider_optn', array(
    'label'       => esc_html__( 'Slide Auto Play', 'm-shop' ),
    'section'     => 'm_shop_product_tab_image',
    'type'        => 'toggle',
    'settings'    => 'm_shop_product_img_sec_slider_optn',
  ) ) );

  $wp_customize->add_setting('m_shop_product_img_sec_adimg', array(
        'default'       => '',
        'capability'    => 'edit_theme_options',
        'sanitize_callback' => 'm_shop_sanitize_upload',
    ));
$wp_customize->add_control( new WP_Customize_Image_Control($wp_customize, 'm_shop_product_img_sec_adimg', array(
        'label'          => __('Upload Image', 'm-shop'),
        'section'        => 'm_shop_product_tab_image',
        'settings'       => 'm_shop_product_img_sec_adimg',
 )));

$wp_customize->add_setting('m_shop_product_img_sec_side', array(
        'default'        => 'left',
        'capability'     => 'edit_theme_options',
        'sanitize_callback' => 'm_shop_sanitize_select',
    ));
$wp_customize->add_control( 'm_shop_product_img_sec_side', array(
        'settings' => 'm_shop_product_img_sec_side',
        'label'   => __('PLace Image On','m-shop'),
        'section' => 'm_shop_product_tab_image',
        'type'    => 'select',
        'choices'    => array(
        'left'     => __('Left','m-shop'),
        'right'     => __('Right','m-shop'),
            
        ),
    ));
$wp_customize->add_setting( 'm_shop_product_img_sec_single_row_slide', array(
                'default'               => true,
                'sanitize_callback'     => 'm_shop_sanitize_checkbox',
            ) );
$wp_customize->add_control( new WP_Customize_Control( $wp_customize, 'm_shop_product_img_sec_single_row_slide', array(
                'label'                 => esc_html__('Enable Single Row Slide', 'm-shop'),
                'type'                  => 'checkbox',
                'section'               => 'm_shop_product_tab_image',
                'settings'              => 'm_shop_product_img_sec_single_row_slide',
            ) ) );
$wp_customize->add_setting('m_shop_product_img_sec_doc', array(
    'sanitize_callback' => 'm_shop_sanitize_text',
    ));
$wp_customize->add_control(new M_Shop_Misc_Control( $wp_customize, 'm_shop_product_img_sec_doc',
            array(
        'section'   => 'm_shop_product_tab_image',
        'type'      => 'doc-link',
        'url'       => 'https://themehunk.com/docs/m-shop/#product-tab',
        'description' => esc_html__( 'To know more go with this', 'm-shop' ),
        'priority'   =>100,
    )));