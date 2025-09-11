<div class="footer_part mt-4">
    <div class="container-fluid">
        <div class="row">
            <div class="col-lg-12">
                <div class="footer_iner text-center">
                    <p>2025 © Influence - Designed by <a href="#"> <i class="ti-heart"></i> </a><a
                            href="#"> Dashboard</a></p>
                </div>
            </div>
        </div>
    </div>
</div>
</section>

<script src="https://cdn.jsdelivr.net/npm/bootstrap@5.3.6/dist/js/bootstrap.bundle.min.js" integrity="sha384-j1CDi7MgGQ12Z7Qab0qlWQ/Qqz24Gc6BM0thvEMVjHnfYGF0rmFCozFSxQBxwHKO" crossorigin="anonymous"></script>

<script src="{{ asset('admins/assets/js/jquery1-3.4.1.min.js') }}"></script>

<script src="{{ asset('admins/assets/js/popper1.min.js') }}"></script>

<script src="{{ asset('admins/assets/js/metisMenu.js') }}"></script>

<script src="{{ asset('admins/assets/vendors/count_up/jquery.waypoints.min.js') }}"></script>

<script src="{{ asset('admins/assets/vendors/chartlist/Chart.min.js') }}"></script>

<script src="{{ asset('admins/assets/vendors/count_up/jquery.counterup.min.js') }}"></script>

<script src="{{ asset('admins/assets/vendors/niceselect/js/jquery.nice-select.min.js') }}"></script>

<script src="{{ asset('admins/assets/vendors/owl_carousel/js/owl.carousel.min.js') }}"></script>

<script src="{{ asset('admins/assets/vendors/datatable/js/jquery.dataTables.min.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/datatable/js/dataTables.responsive.min.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/datatable/js/dataTables.buttons.min.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/datatable/js/buttons.flash.min.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/datatable/js/jszip.min.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/datatable/js/pdfmake.min.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/datatable/js/vfs_fonts.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/datatable/js/buttons.html5.min.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/datatable/js/buttons.print.min.js') }}"></script>

<script src="{{ asset('admins/assets/vendors/datepicker/datepicker.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/datepicker/datepicker.en.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/datepicker/datepicker.custom.js') }}"></script>
<script src="{{ asset('admins/assets/js/chart.min.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/chartjs/roundedBar.min.js') }}"></script>

<script src="{{ asset('admins/assets/vendors/progressbar/jquery.barfiller.js') }}"></script>

<script src="{{ asset('admins/assets/vendors/tagsinput/tagsinput.js') }}"></script>

<script src="{{ asset('admins/assets/vendors/text_editor/summernote-bs4.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/am_chart/amcharts.js') }}"></script>

<script src="{{ asset('admins/assets/vendors/scroll/perfect-scrollbar.min.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/scroll/scrollable-custom.js') }}"></script>

<script src="{{ asset('admins/assets/vendors/vectormap-home/vectormap-2.0.2.min.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/vectormap-home/vectormap-world-mill-en.js') }}"></script>

<script src="{{ asset('admins/assets/vendors/apex_chart/apex-chart2.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/apex_chart/apex_dashboard.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/echart/echarts.min.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/chart_am/core.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/chart_am/charts.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/chart_am/animated.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/chart_am/kelly.js') }}"></script>
<script src="{{ asset('admins/assets/vendors/chart_am/chart-custom.js') }}"></script>

<script src="{{ asset('admins/assets/js/dashboard_init.js') }}"></script>
<script src="{{ asset('admins/assets/js/custom.js') }}"></script>
     <script>
            @if (session('success'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'success',
                    title: '{{ session('success') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif

            @if (session('error'))
                Swal.fire({
                    toast: true,
                    position: 'top-end',
                    icon: 'error',
                    title: '{{ session('error') }}',
                    showConfirmButton: false,
                    timer: 3000,
                    timerProgressBar: true
                });
            @endif
        </script>

</body>

<!-- Mirrored from demo.dashboardpack.com/sales-html/ by HTTrack Website Copier/3.x [XR&CO'2014], Fri, 24 May 2024 07:24:00 GMT -->

</html>