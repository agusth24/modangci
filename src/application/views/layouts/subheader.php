<!-- BEGIN: Subheader -->
<!-- begin:: Content Head -->
<div class="kt-subheader  kt-grid__item" id="kt_subheader">
	<div class="kt-container  kt-container--fluid ">
		<div class="kt-subheader__main">
			<h3 class="kt-subheader__title"><?= $page_judul ?></h3>
			<span class="kt-subheader__separator kt-subheader__separator--v"></span>
			<span class="kt-subheader__desc"><?= $breadcrumb->susrmdgroupDisplay ?></span>

			<?php
			if ($breadcrumb->susrmdgroupDisplay != $breadcrumb->susrmodulNamaDisplay) :
				?>
				<span class="kt-subheader__separator kt-subheader__separator--v"></span>
				<span class="kt-subheader__desc"><?= $breadcrumb->susrmodulNamaDisplay ?></span>
			<?php
			endif;
			?>

			<?php
			if (isset($status_page)) :
				?>
				<span class="kt-subheader__separator kt-subheader__separator--v"></span>
				<span class="kt-subheader__desc"><?= $status_page ?></span>
			<?php
			endif;
			?>
		</div>
		<div class="kt-subheader__toolbar">
			<div class="kt-subheader__wrapper">
				<a href="#" class="btn kt-subheader__btn-daterange" id="kt_dashboard_daterangepicker" data-toggle="kt-tooltip" title="Today is good day!!" data-placement="left">
					<span class="kt-subheader__btn-daterange-title" id="kt_dashboard_daterangepicker_title">Today:</span>&nbsp;
					<span class="kt-subheader__btn-daterange-date" id="kt_dashboard_daterangepicker_date"><?= date('M d, Y') ?></span>
					<i class="flaticon2-calendar-1"></i>
				</a>
			</div>
		</div>
	</div>
</div>

<!-- end:: Content Head -->