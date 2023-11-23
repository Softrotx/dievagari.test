/* Make Beaver options play nice with the theme */

<?php global $vamtam_theme ?>

<?php if ( class_exists( 'FLBuilderModel' ) ) : ?>
:root {
	--vamtam-beaver-global-module_margins_top: var( --vamtam-beaver-global-module_margins_top_large );
	--vamtam-beaver-global-module_margins_right: var( --vamtam-beaver-global-module_margins_right_large );
	--vamtam-beaver-global-module_margins_bottom: var( --vamtam-beaver-global-module_margins_bottom_large );
	--vamtam-beaver-global-module_margins_left: var( --vamtam-beaver-global-module_margins_left_large );
}
<?php endif ?>

.vamtam-box-outer-padding,
.limit-wrapper,
.header-padding {
	padding-left: calc( var( --vamtam-box-outer-padding ) + var( --vamtam-block-margin-desktop-left, var( --vamtam-beaver-global-module_margins_left ) ) );
	padding-right: calc( var( --vamtam-box-outer-padding ) + var( --vamtam-block-margin-desktop-right, var( --vamtam-beaver-global-module_margins_right ) ) );
}

body:not(.single-tribe_events):not(.post-type-archive).fl-builder #main > .limit-wrapper,
body .fl-row-content-wrap {
	padding-left: var( --vamtam-box-outer-padding );
	padding-right: var( --vamtam-box-outer-padding );
}

body:not(.single-tribe_events):not(.post-type-archive).fl-builder #main > .limit-wrapper {
	max-width: calc( var( --vamtam-site-max-width ) + var( --vamtam-beaver-global-module_margins_left ) + var( --vamtam-beaver-global-module_margins_right ) );
}

<?php if ( isset( $vamtam_theme['site-layout-type'] ) && 'full' !== $vamtam_theme['site-layout-type'] ) : ?>
	#page,
	.fixed-header-box,
	.fl-row-fixed-width {
		max-width: calc( var( --vamtam-site-max-width ) + 2 * var( --vamtam-box-outer-padding ) + var( --vamtam-block-margin-desktop-left, var( --vamtam-beaver-global-module_margins_left ) ) + var( --vamtam-block-margin-desktop-right, var( --vamtam-beaver-global-module_margins_right ) ) );
	}
<?php endif ?>

.vamtam-box-outer-padding .vamtam-box-outer-padding,
body .vamtam-box-outer-padding .fl-row-content-wrap,
.limit-wrapper .limit-wrapper {
	padding-left: 0;
	padding-right: 0;
}

body .post-content .fl-row-full-width .fl-row-fixed-width {
	padding-left: calc( 20px + var( --vamtam-block-margin-desktop-left, var( --vamtam-beaver-global-module_margins_left ) ) );
	padding-right: calc( 20px + var( --vamtam-block-margin-desktop-right, var( --vamtam-beaver-global-module_margins_right ) ) );
}

@media ( max-width: <?php echo intval( $content_width + 200 ) ?>px ) {
	.post-siblings {
		display: none;
	}
}

@media ( min-width: <?php echo intval( $medium_breakpoint + 1 ) ?>px ) and ( max-width: <?php echo intval( $content_width ) ?>px ) {
	.vamtam-box-outer-padding,
	.limit-wrapper,
	.header-padding {
		padding-left: calc( 30px + var( --vamtam-block-margin-desktop-left, var( --vamtam-beaver-global-module_margins_left ) ) );
		padding-right: calc( 30px + var( --vamtam-block-margin-desktop-right, var( --vamtam-beaver-global-module_margins_right ) ) );
	}

	body:not(.single-tribe_events):not(.post-type-archive).fl-builder #main > .limit-wrapper,
	body .fl-row-content-wrap {
		padding-left: 30px;
		padding-right: 30px;
	}

	body .post-content .fl-row-full-width .fl-row-fixed-width {
		padding-left: calc( 20px + var( --vamtam-beaver-global-module_margins_left ) );
		padding-right: calc( 20px + var( --vamtam-beaver-global-module_margins_right ) );
	}

	body.single-post .post-content .fl-row-fixed-width {
		max-width: 840px;
	}
}

@media ( max-width: <?php echo intval( $medium_breakpoint ) ?>px ) {
	<?php if ( class_exists( 'FLBuilderModel' ) ) : ?>
	:root {
		--vamtam-beaver-global-module_margins_top: var( --vamtam-beaver-global-module_margins_top_medium );
		--vamtam-beaver-global-module_margins_right: var( --vamtam-beaver-global-module_margins_right_medium );
		--vamtam-beaver-global-module_margins_bottom: var( --vamtam-beaver-global-module_margins_bottom_medium );
		--vamtam-beaver-global-module_margins_left: var( --vamtam-beaver-global-module_margins_left_medium );
	}
	<?php endif ?>

	.vamtam-box-outer-padding,
	.limit-wrapper,
	.header-padding {
		padding-left: calc( 20px + var( --vamtam-block-margin-tablet-left, var( --vamtam-beaver-global-module_margins_left ) ) );
		padding-right: calc( 20px + var( --vamtam-block-margin-tablet-right, var( --vamtam-beaver-global-module_margins_right ) ) );
	}

	body:not(.single-tribe_events):not(.post-type-archive).fl-builder #main > .limit-wrapper,
	body .fl-row-content-wrap {
		padding-left: 20px;
		padding-right: 20px;
	}

	body .post-content .fl-row-full-width .fl-row-fixed-width {
		padding-left: calc( 10px + var( --vamtam-beaver-global-module_margins_left ) );
		padding-right: calc( 10px + var( --vamtam-beaver-global-module_margins_right ) );
	}

	body:not(.single-tribe_events):not(.post-type-archive).fl-builder #main:not(.layout-full) .limit-wrapper {
		padding-left: 0;
		padding-right: 0;
	}

	body .post-content .fl-row-full-width .fl-row-fixed-width,
	body #main:not(.layout-full) .fl-builder-content > .fl-row-full-width .fl-row-fixed-width,
	body #main:not(.layout-full) .fl-builder-content > .fl-row-full-width .fl-row-full-width,
	body #main:not(.layout-full) .fl-builder-content > .fl-row-fixed-width {
		padding-left: calc( 10px + var( --vamtam-block-margin-tablet-left, var( --vamtam-beaver-global-module_margins_left ) ) );
		padding-right: calc( 10px + var( --vamtam-block-margin-tablet-right, var( --vamtam-beaver-global-module_margins_right ) ) );
	}
}

@media ( max-width: <?php echo intval( $small_breakpoint ) ?>px ) {
	<?php if ( class_exists( 'FLBuilderModel' ) ) : ?>
	:root {
		--vamtam-beaver-global-module_margins_top: var( --vamtam-beaver-global-module_margins_top_responsive );
		--vamtam-beaver-global-module_margins_right: var( --vamtam-beaver-global-module_margins_right_responsive );
		--vamtam-beaver-global-module_margins_bottom: var( --vamtam-beaver-global-module_margins_bottom_responsive );
		--vamtam-beaver-global-module_margins_left: var( --vamtam-beaver-global-module_margins_left_responsive );
	}
	<?php endif ?>

	.vamtam-box-outer-padding,
	.limit-wrapper,
	.header-padding {
		padding-left: calc( 10px + var( --vamtam-block-margin-phone-left, var( --vamtam-beaver-global-module_margins_left ) ) );
		padding-right: calc( 10px + var( --vamtam-block-margin-phone-right, var( --vamtam-beaver-global-module_margins_right ) ) );
	}

	body #main:not(.layout-full) .fl-builder-content > .fl-row-full-width .fl-row-fixed-width,
	body #main:not(.layout-full) .fl-builder-content > .fl-row-full-width .fl-row-full-width,
	body #main:not(.layout-full) .fl-builder-content > .fl-row-fixed-width {
		padding-left: 0;
		padding-right: 0;
	}
}

<?php if ( ! class_exists( 'FLBuilderModel' ) || ! FLBuilderModel::is_builder_active() ) : ?>
	:root { scroll-behavior: smooth; }
<?php endif ?>
