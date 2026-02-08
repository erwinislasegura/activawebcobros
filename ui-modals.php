<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Modals"; include('partials/title-meta.php'); ?>

    <?php include('partials/head-css.php'); ?>
</head>

<body>
    <!-- Begin page -->
    <div class="wrapper">

        <?php include('partials/menu.php'); ?>

        <!-- ============================================================== -->
        <!-- Start Main Content -->
        <!-- ============================================================== -->

        <div class="content-page">

            <div class="container-fluid">
                <?php $subtitle = "UI"; $title = "Modals"; include('partials/page-title.php'); ?> 
                
                <div class="row">
                
                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Bootstrap Modals</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">
                                    A rendered modal with header, body, and set of actions in the footer.
                                </p>

                                <!-- Standard modal content -->
                                <div id="standard-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="standard-modalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="standard-modalLabel">Modal Heading</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h5>Text in a modal</h5>
                                                <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula.</p>
                                                <hr>
                                                <h5>Overflowing text to show scroll behavior</h5>
                                                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
                                                <p class="mb-0">Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                                <!--  Modal content for the Large example -->
                                <div class="modal fade" id="bs-example-modal-lg" tabindex="-1" role="dialog" aria-labelledby="myLargeModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-lg">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myLargeModalLabel">Large modal</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ...
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                                <div class="modal fade" id="bs-example-modal-sm" tabindex="-1" role="dialog" aria-labelledby="mySmallModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-sm">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="mySmallModalLabel">Small modal</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ...
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                                <!-- Full width modal content -->
                                <div id="full-width-modal" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="fullWidthModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-full-width">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="fullWidthModalLabel">Modal Heading</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h5>Text in a modal</h5>
                                                <p>Duis mollis, est non commodo luctus, nisi erat porttitor ligula.</p>
                                                <hr>
                                                <h5>Overflowing text to show scroll behavior</h5>
                                                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
                                                <p class="mb-0">Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                                <!-- Long Content Scroll Modal -->
                                <div class="modal fade" id="scrollable-modal" tabindex="-1" role="dialog" aria-labelledby="scrollableModalTitle" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-scrollable" role="document">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="scrollableModalTitle">Modal title</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas
                                                    eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue
                                                    laoreet rutrum faucibus dolor auctor.</p>
                                                <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl
                                                    consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
                                                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas
                                                    eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue
                                                    laoreet rutrum faucibus dolor auctor.</p>
                                                <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl
                                                    consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
                                                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas
                                                    eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue
                                                    laoreet rutrum faucibus dolor auctor.</p>
                                                <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl
                                                    consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
                                                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas
                                                    eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue
                                                    laoreet rutrum faucibus dolor auctor.</p>
                                                <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl
                                                    consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
                                                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas
                                                    eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue
                                                    laoreet rutrum faucibus dolor auctor.</p>
                                                <p>Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl
                                                    consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
                                                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas
                                                    eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                <p>Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue
                                                    laoreet rutrum faucibus dolor auctor.</p>
                                                <p class="mb-0">Aenean lacinia bibendum nulla sed consectetur. Praesent commodo cursus magna, vel scelerisque nisl
                                                    consectetur et. Donec sed odio dui. Donec ullamcorper nulla non metus auctor fringilla.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                                <div class="d-flex flex-wrap gap-2">
                                    <!-- Standard  modal -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#standard-modal">Standard Modal</button>
                                    <!-- Large modal -->
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#bs-example-modal-lg">Large Modal</button>
                                    <!-- Small modal -->
                                    <button type="button" class="btn btn-success" data-bs-toggle="modal" data-bs-target="#bs-example-modal-sm">Small Modal</button>
                                    <!-- Full width modal -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#full-width-modal">Full Width Modal</button>
                                    <!-- Scrollable modal -->
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#scrollable-modal">Scrollable Modal</button>
                                </div>

                            </div> <!-- end card body-->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Modal Position</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Specify the position for the modal. You can display modal at top, bottom, or center of page by specifying
                                    classes <code>modal-top</code>, <code>modal-bottom</code>and <code>modal-dialog-centered</code>
                                    respectively.</p>

                                <!-- Top modal content -->
                                <div id="top-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-top">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="topModalLabel">Modal Heading</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h5 class="mt-0">Text in a modal</h5>
                                                <p class="mb-0">Duis mollis, est non commodo luctus, nisi erat porttitor ligula.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                                <!-- Bottom modal content -->
                                <div id="bottom-modal" class="modal fade" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-bottom">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="bottomModalLabel">Modal Heading</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h5 class="mt-0">Text in a modal</h5>
                                                <p class="mb-0">Duis mollis, est non commodo luctus, nisi erat porttitor ligula.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-light" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary">Save changes</button>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                                <!-- Center modal content -->
                                <div class="modal fade" id="centermodal" tabindex="-1" role="dialog" aria-hidden="true">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="myCenterModalLabel">Center modal</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h5 class="mt-0">Overflowing text to show scroll behavior</h5>
                                                <p>Cras mattis consectetur purus sit amet fermentum. Cras justo odio, dapibus ac facilisis in, egestas eget quam. Morbi leo risus, porta ac consectetur ac, vestibulum at eros.</p>
                                                <p class="mb-0">Praesent commodo cursus magna, vel scelerisque nisl consectetur et. Vivamus sagittis lacus vel augue laoreet rutrum faucibus dolor auctor.</p>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                                <div class="d-flex flex-wrap gap-2">
                                    <!-- Top modal -->
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#top-modal">Top Modal</button>
                                    <!-- Bottom modal -->
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#bottom-modal">Bottom Modal</button>
                                    <!-- Center modal -->
                                    <button type="button" class="btn btn-secondary" data-bs-toggle="modal" data-bs-target="#centermodal">Center modal</button>
                                </div>
                            </div> <!-- end card body-->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Multiple Modal</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Display a series of modals one by one to guide your users on multiple aspects or take step wise input.</p>

                                <!-- Modal Heading -->
                                <div id="multiple-one" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="multiple-oneModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="multiple-oneModalLabel">Modal Heading</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h5 class="mt-0">Text in a modal</h5>
                                                <p class="mb-0">Duis mollis, est non commodo luctus, nisi erat porttitor ligula.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-bs-target="#multiple-two" data-bs-toggle="modal" data-bs-dismiss="modal">Next</button>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                                <!-- Modal Heading -->
                                <div id="multiple-two" class="modal fade" tabindex="-1" role="dialog" aria-labelledby="multiple-twoModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h4 class="modal-title" id="multiple-twoModalLabel">Modal Heading</h4>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <h5 class="mt-0">Text in a modal</h5>
                                                <p class="mb-0">Duis mollis, est non commodo luctus, nisi erat porttitor ligula.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-primary" data-bs-dismiss="modal">Close</button>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                                <div class="d-flex flex-wrap gap-2">
                                    <!-- Multiple modal -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#multiple-one">Multiple Modal</button>
                                </div>
                            </div> <!-- end card body-->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Toggle Between Modals</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Toggle between multiple modals with some clever placement of the <code>data-bs-target</code> and <code>data-bs-toggle</code> attributes.</p>

                                <!-- Modal 1-->
                                <div class="modal fade" id="exampleModalToggle" aria-hidden="true" aria-labelledby="exampleModalToggleLabel" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalToggleLabel">Modal 1</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Show a second modal and hide this one with the button below.
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-primary" data-bs-target="#exampleModalToggle2" data-bs-toggle="modal" data-bs-dismiss="modal">Open second modal</button>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                                <!-- Modal 2-->
                                <div class="modal fade" id="exampleModalToggle2" aria-hidden="true" aria-labelledby="exampleModalToggleLabel2" tabindex="-1">
                                    <div class="modal-dialog modal-dialog-centered">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalToggleLabel2">Modal 2</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                Hide this modal and show the first with the button below.
                                            </div>
                                            <div class="modal-footer">
                                                <button class="btn btn-primary" data-bs-target="#exampleModalToggle" data-bs-toggle="modal" data-bs-dismiss="modal">Back to first</button>
                                            </div>
                                        </div><!-- /.modal-content -->
                                    </div><!-- /.modal-dialog -->
                                </div><!-- /.modal -->

                                <a class="btn btn-secondary" data-bs-toggle="modal" href="#exampleModalToggle" role="button">Open first modal</a>

                            </div> <!-- end card body-->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Fullscreen Modal</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Another override is the option to pop up a modal that covers the user viewport, available via modifier classes that are placed on a <code>.modal-dialog</code></p>

                                <div class="hstack gap-2 flex-wrap">
                                    <!-- Fullscreen Modal -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#fullscreeexampleModal"> Fullscreen Modal</button>
                                    <!-- Full Screen Below sm -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalFullscreenSm">Full Screen Below sm</button>
                                    <!-- Full Screen Below md -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalFullscreenMd">Full Screen Below md</button>
                                    <!-- Full Screen Below lg -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalFullscreenLg">Full Screen Below lg</button>
                                    <!-- Full Screen Below xl -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalFullscreenXl">Full Screen Below xl</button>
                                    <!-- Full Screen Below xxl -->
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModalFullscreenXxl">Full Screen Below xxl</button>
                                </div>

                                <!-- Full Screen Modal -->
                                <div class="modal fade" id="fullscreeexampleModal" tabindex="-1" aria-labelledby="fullscreeexampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-fullscreen">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="fullscreeexampleModalLabel">Full Screen Modal</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ...
                                            </div>
                                            <div class="modal-footer">
                                                <a href="javascript:void(0);" class="btn btn-light" data-bs-dismiss="modal">Close</a>
                                                <button type="button" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Full screen below sm -->
                                <div class="modal fade" id="exampleModalFullscreenSm" tabindex="-1" aria-labelledby="exampleModalFullscreenSmLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-fullscreen-sm-down">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalFullscreenSmLabel">Full screen below sm</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ...
                                            </div>
                                            <div class="modal-footer">
                                                <a href="javascript:void(0);" class="btn btn-light" data-bs-dismiss="modal">Close</a>
                                                <button type="button" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Full screen below md -->
                                <div class="modal fade" id="exampleModalFullscreenMd" tabindex="-1" aria-labelledby="exampleModalFullscreenMdLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-fullscreen-md-down">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalFullscreenMdLabel">Full screen below md</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ...
                                            </div>
                                            <div class="modal-footer">
                                                <a href="javascript:void(0);" class="btn btn-light" data-bs-dismiss="modal">Close</a>
                                                <button type="button" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Full screen below lg -->
                                <div class="modal fade" id="exampleModalFullscreenLg" tabindex="-1" aria-labelledby="exampleModalFullscreenLgLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-fullscreen-lg-down">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalFullscreenLgLabel">Full screen below lg</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ...
                                            </div>
                                            <div class="modal-footer">
                                                <a href="javascript:void(0);" class="btn btn-light" data-bs-dismiss="modal">Close</a>
                                                <button type="button" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Full screen below xl -->
                                <div class="modal fade" id="exampleModalFullscreenXl" tabindex="-1" aria-labelledby="exampleModalFullscreenXlLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-fullscreen-sm-down">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalFullscreenXlLabel">Full screen below xl</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ...
                                            </div>
                                            <div class="modal-footer">
                                                <a href="javascript:void(0);" class="btn btn-light" data-bs-dismiss="modal">Close</a>
                                                <button type="button" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                                <!-- Full screen below xxl -->
                                <div class="modal fade" id="exampleModalFullscreenXxl" tabindex="-1" aria-labelledby="exampleModalFullscreenXxlLabel" aria-hidden="true">
                                    <div class="modal-dialog modal-fullscreen-xxl-down">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalFullscreenXxlLabel">Full screen below xxl</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                ...
                                            </div>
                                            <div class="modal-footer">
                                                <a href="javascript:void(0);" class="btn btn-light" data-bs-dismiss="modal">Close</a>
                                                <button type="button" class="btn btn-primary">Save Changes</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div> <!-- end card body-->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Static Backdrop</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">When backdrop is set to static, the modal will not close when clicking outside it. Click the button below to try it.</p>

                                <div class="d-flex flex-wrap gap-2">
                                    <!-- Static Backdrop modal -->
                                    <button type="button" class="btn btn-info" data-bs-toggle="modal" data-bs-target="#staticBackdrop">
                                        Static Backdrop
                                    </button>
                                </div> <!-- btn list -->

                                <!-- Modal -->
                                <div class="modal fade" id="staticBackdrop" data-bs-backdrop="static" data-bs-keyboard="false" tabindex="-1" aria-labelledby="staticBackdropLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="staticBackdropLabel">Modal title</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div> <!-- end modal header -->
                                            <div class="modal-body">
                                                <p class="m-0">I will not close if you click outside me. Don't even try to press escape key.</p>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary">Understood</button>
                                            </div> <!-- end modal footer -->
                                        </div> <!-- end modal content-->
                                    </div> <!-- end modal dialog-->
                                </div> <!-- end modal-->

                            </div> <!-- end card body-->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                    <div class="col-xl-12">
                        <div class="card">
                            <div class="card-header">
                                <div class="flex-grow-1">
                                    <h4 class="card-title">Varying Modal Content</h4>
                                </div>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <p class="text-muted">Have a bunch of buttons that all trigger the same modal with slightly different contents? Use
                                    <code>event.relatedTarget</code> and <a href="https://developer.mozilla.org/en-US/docs/Learn/HTML/Howto/Use_data_attributes" target="_blank">HTML <code>data-bs-*</code>
                                        attributes</a> to vary the contents of the modal depending on which button was clicked.
                                </p>

                                <div class="hstack gap-2 flex-wrap">
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@mdo">Open modal for @mdo</button>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@fat">Open modal for @fat</button>
                                    <button type="button" class="btn btn-primary" data-bs-toggle="modal" data-bs-target="#exampleModal" data-bs-whatever="@getbootstrap">Open modal for @getbootstrap</button>
                                </div>

                                <div class="modal fade" id="exampleModal" tabindex="-1" aria-labelledby="exampleModalLabel" aria-hidden="true">
                                    <div class="modal-dialog">
                                        <div class="modal-content">
                                            <div class="modal-header">
                                                <h5 class="modal-title" id="exampleModalLabel">New message</h5>
                                                <button type="button" class="btn-close" data-bs-dismiss="modal" aria-label="Close"></button>
                                            </div>
                                            <div class="modal-body">
                                                <form>
                                                    <div class="mb-3">
                                                        <label for="recipient-name" class="col-form-label">Recipient:</label>
                                                        <input type="text" class="form-control" id="recipient-name">
                                                    </div>
                                                    <div class="mb-3">
                                                        <label for="message-text" class="col-form-label">Message:</label>
                                                        <textarea class="form-control" id="message-text"></textarea>
                                                    </div>
                                                </form>
                                            </div>
                                            <div class="modal-footer">
                                                <button type="button" class="btn btn-secondary" data-bs-dismiss="modal">Close</button>
                                                <button type="button" class="btn btn-primary">Send message</button>
                                            </div>
                                        </div>
                                    </div>
                                </div>

                            </div> <!-- end card body-->
                        </div> <!-- end card-->
                    </div> <!-- end col -->

                </div> <!-- end row -->

            </div> <!-- container -->

            <?php include('partials/footer.php'); ?>

        </div>

        <!-- ============================================================== -->
        <!-- End of Main Content -->
        <!-- ============================================================== -->

    </div>
    <!-- END wrapper -->

    <?php include('partials/customizer.php'); ?>

    <?php include('partials/footer-scripts.php'); ?>

    <script>
        const exampleModal = document.getElementById('exampleModal')
        exampleModal.addEventListener('show.bs.modal', event => {
            // Button that triggered the modal
            const button = event.relatedTarget
            // Extract info from data-bs-* attributes
            const recipient = button.getAttribute('data-bs-whatever')
            // If necessary, you could initiate an AJAX request here
            // and then do the updating in a callback.
            //
            // Update the modal's content.
            const modalTitle = exampleModal.querySelector('.modal-title')
            const modalBodyInput = exampleModal.querySelector('.modal-body input')

            modalTitle.textContent = `New message to ${recipient}`
            modalBodyInput.value = recipient
        })
    </script>

</body>

</html>