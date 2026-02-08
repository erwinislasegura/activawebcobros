<?php include('partials/html.php'); ?>

<head>
    <?php $title = "Accordions"; include('partials/title-meta.php'); ?>

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
                <?php $subtitle = "UI"; $title = "Accordions"; include('partials/page-title.php'); ?> 

                <div class="row">
                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Default Accordions</h4>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="accordion" id="accordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#collapseOne" aria-expanded="true" aria-controls="collapseOne">
                                                Accordion Item #1
                                            </button>
                                        </h2>
                                        <div id="collapseOne" class="accordion-collapse collapse show" aria-labelledby="headingOne" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse
                                                plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                                appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                                custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                                within the <code>.accordion-body</code>, though the transition does limit overflow.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseTwo" aria-expanded="false" aria-controls="collapseTwo">
                                                Accordion Item #2
                                            </button>
                                        </h2>
                                        <div id="collapseTwo" class="accordion-collapse collapse" aria-labelledby="headingTwo" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse
                                                plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                                appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                                custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                                within the <code>.accordion-body</code>, though the transition does limit overflow.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="headingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#collapseThree" aria-expanded="false" aria-controls="collapseThree">
                                                Accordion Item #3
                                            </button>
                                        </h2>
                                        <div id="collapseThree" class="accordion-collapse collapse" aria-labelledby="headingThree" data-bs-parent="#accordionExample">
                                            <div class="accordion-body">
                                                <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse
                                                plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                                appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                                custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                                within the <code>.accordion-body</code>, though the transition does limit overflow.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Flush Accordions</h4>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="accordion accordion-flush" id="accordionFlushExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseOne" aria-expanded="false" aria-controls="flush-collapseOne">
                                                Accordion Item #1
                                            </button>
                                        </h2>
                                        <div id="flush-collapseOne" class="accordion-collapse collapse show" aria-labelledby="flush-headingOne" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">
                                                <p class="m-0">Placeholder content for this accordion, which is intended to demonstrate the
                                                    <code>.accordion-flush</code> class. This is the first item's accordion body.
                                                </p>
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseTwo" aria-expanded="false" aria-controls="flush-collapseTwo">
                                                Accordion Item #2
                                            </button>
                                        </h2>
                                        <div id="flush-collapseTwo" class="accordion-collapse collapse" aria-labelledby="flush-headingTwo" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">Placeholder content for this accordion, which is intended to demonstrate the
                                                <code>.accordion-flush</code> class. This is the second item's accordion body. Let's imagine this being
                                                filled with some actual content.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="flush-headingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#flush-collapseThree" aria-expanded="false" aria-controls="flush-collapseThree">
                                                Accordion Item #3
                                            </button>
                                        </h2>
                                        <div id="flush-collapseThree" class="accordion-collapse collapse" aria-labelledby="flush-headingThree" data-bs-parent="#accordionFlushExample">
                                            <div class="accordion-body">Placeholder content for this accordion, which is intended to demonstrate the
                                                <code>.accordion-flush</code> class. This is the third item's accordion body. Nothing more exciting
                                                happening here in terms of content, but just filling up the space to make it look, at least at first
                                                glance, a bit more representative of how this would look in a real-world application.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Always Open Accordions</h4>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="accordion" id="accordionPanelsStayOpenExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseOne" aria-expanded="true" aria-controls="panelsStayOpen-collapseOne">
                                                Accordion Item #1
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseOne" class="accordion-collapse collapse show" aria-labelledby="panelsStayOpen-headingOne">
                                            <div class="accordion-body">
                                                <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse
                                                plugin adds the appropriate classes that we use to style each element. These classes control the
                                                overall appearance, as well as the showing and hiding via CSS transitions. You can modify any of
                                                this with custom CSS or overriding our default variables. It's also worth noting that just about any
                                                HTML can go within the <code>.accordion-body</code>, though the transition does limit overflow.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseTwo" aria-expanded="false" aria-controls="panelsStayOpen-collapseTwo">
                                                Accordion Item #2
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseTwo" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingTwo">
                                            <div class="accordion-body">
                                                <strong>This is the second item's accordion body.</strong> It is hidden by default, until the
                                                collapse plugin adds the appropriate classes that we use to style each element. These classes
                                                control the overall appearance, as well as the showing and hiding via CSS transitions. You can
                                                modify any of this with custom CSS or overriding our default variables. It's also worth noting that
                                                just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit
                                                overflow.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="panelsStayOpen-headingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#panelsStayOpen-collapseThree" aria-expanded="false" aria-controls="panelsStayOpen-collapseThree">
                                                Accordion Item #3
                                            </button>
                                        </h2>
                                        <div id="panelsStayOpen-collapseThree" class="accordion-collapse collapse" aria-labelledby="panelsStayOpen-headingThree">
                                            <div class="accordion-body">
                                                <strong>This is the third item's accordion body.</strong> It is hidden by default, until the
                                                collapse plugin adds the appropriate classes that we use to style each element. These classes
                                                control the overall appearance, as well as the showing and hiding via CSS transitions. You can
                                                modify any of this with custom CSS or overriding our default variables. It's also worth noting that
                                                just about any HTML can go within the <code>.accordion-body</code>, though the transition does limit
                                                overflow.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Accordion Without Arrow </h4>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="accordion accordion-arrow-none" id="withoutarrowaccordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="withoutarrowheadingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#withoutarrowcollapseOne" aria-expanded="true" aria-controls="withoutarrowcollapseOne">
                                                Accordion Item #1
                                            </button>
                                        </h2>
                                        <div id="withoutarrowcollapseOne" class="accordion-collapse collapse show" aria-labelledby="withoutarrowheadingOne" data-bs-parent="#withoutarrowaccordionExample">
                                            <div class="accordion-body">
                                                <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse
                                                plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                                appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                                custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                                within the <code>.accordion-body</code>, though the transition does limit overflow.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="withoutarrowheadingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#withoutarrowcollapseTwo" aria-expanded="false" aria-controls="withoutarrowcollapseTwo">
                                                Accordion Item #2
                                            </button>
                                        </h2>
                                        <div id="withoutarrowcollapseTwo" class="accordion-collapse collapse" aria-labelledby="withoutarrowheadingTwo" data-bs-parent="#withoutarrowaccordionExample">
                                            <div class="accordion-body">
                                                <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse
                                                plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                                appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                                custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                                within the <code>.accordion-body</code>, though the transition does limit overflow.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="withoutarrowheadingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#withoutarrowcollapseThree" aria-expanded="false" aria-controls="withoutarrowcollapseThree">
                                                Accordion Item #3
                                            </button>
                                        </h2>
                                        <div id="withoutarrowcollapseThree" class="accordion-collapse collapse" aria-labelledby="withoutarrowheadingThree" data-bs-parent="#withoutarrowaccordionExample">
                                            <div class="accordion-body">
                                                <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse
                                                plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                                appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                                custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                                within the <code>.accordion-body</code>, though the transition does limit overflow.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Bordered Accordions</h4>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="accordion accordion-bordered" id="BorderedaccordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="BorderedheadingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#BorderedcollapseOne" aria-expanded="true" aria-controls="BorderedcollapseOne">
                                                Accordion Item #1
                                            </button>
                                        </h2>
                                        <div id="BorderedcollapseOne" class="accordion-collapse collapse show" aria-labelledby="BorderedheadingOne" data-bs-parent="#BorderedaccordionExample">
                                            <div class="accordion-body">
                                                <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse
                                                plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                                appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                                custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                                within the <code>.accordion-body</code>, though the transition does limit overflow.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="BorderedheadingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#BorderedcollapseTwo" aria-expanded="false" aria-controls="BorderedcollapseTwo">
                                                Accordion Item #2
                                            </button>
                                        </h2>
                                        <div id="BorderedcollapseTwo" class="accordion-collapse collapse" aria-labelledby="BorderedheadingTwo" data-bs-parent="#BorderedaccordionExample">
                                            <div class="accordion-body">
                                                <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse
                                                plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                                appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                                custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                                within the <code>.accordion-body</code>, though the transition does limit overflow.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="BorderedheadingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#BorderedcollapseThree" aria-expanded="false" aria-controls="BorderedcollapseThree">
                                                Accordion Item #3
                                            </button>
                                        </h2>
                                        <div id="BorderedcollapseThree" class="accordion-collapse collapse" aria-labelledby="BorderedheadingThree" data-bs-parent="#BorderedaccordionExample">
                                            <div class="accordion-body">
                                                <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse
                                                plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                                appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                                custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                                within the <code>.accordion-body</code>, though the transition does limit overflow.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->

                    <div class="col-xl-6">
                        <div class="card">
                            <div class="card-header">
                                <h4 class="card-title">Custom Icon Accordion</h4>
                                <div class="card-action">
                                    <a href="#!" class="card-action-item" data-action="card-toggle"><i class="ti ti-chevron-up"></i></a>
                                </div>
                            </div>

                            <div class="card-body">
                                <div class="accordion accordion-custom-icon accordion-arrow-none" id="CustomIconaccordionExample">
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="CustomIconheadingOne">
                                            <button class="accordion-button" type="button" data-bs-toggle="collapse" data-bs-target="#CustomIconcollapseOne" aria-expanded="true" aria-controls="CustomIconcollapseOne">
                                                Accordion item with tabler icons
                                                <i class="ti ti-plus accordion-icon accordion-icon-on"></i>
                                                <i class="ti ti-minus accordion-icon accordion-icon-off"></i>
                                            </button>
                                        </h2>
                                        <div id="CustomIconcollapseOne" class="accordion-collapse collapse show" aria-labelledby="CustomIconheadingOne" data-bs-parent="#CustomIconaccordionExample">
                                            <div class="accordion-body">
                                                <strong>This is the first item's accordion body.</strong> It is shown by default, until the collapse
                                                plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                                appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                                custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                                within the <code>.accordion-body</code>, though the transition does limit overflow.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="CustomIconheadingTwo">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#CustomIconcollapseTwo" aria-expanded="false" aria-controls="CustomIconcollapseTwo">
                                                Accordion item with lucid icons
                                                <i data-lucide="plus-circle" class="accordion-icon accordion-icon-on avatar-xxs me-n1"></i>
                                                <i data-lucide="minus-circle" class="accordion-icon accordion-icon-off avatar-xxs me-n1"></i>
                                            </button>
                                        </h2>
                                        <div id="CustomIconcollapseTwo" class="accordion-collapse collapse" aria-labelledby="CustomIconheadingTwo" data-bs-parent="#CustomIconaccordionExample">
                                            <div class="accordion-body">
                                                <strong>This is the second item's accordion body.</strong> It is hidden by default, until the collapse
                                                plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                                appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                                custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                                within the <code>.accordion-body</code>, though the transition does limit overflow.
                                            </div>
                                        </div>
                                    </div>
                                    <div class="accordion-item">
                                        <h2 class="accordion-header" id="CustomIconheadingThree">
                                            <button class="accordion-button collapsed" type="button" data-bs-toggle="collapse" data-bs-target="#CustomIconcollapseThree" aria-expanded="false" aria-controls="CustomIconcollapseThree">
                                                Accordion item with tabler icons
                                                <i class="ti ti-arrow-down accordion-icon accordion-icon-on"></i>
                                                <i class="ti ti-arrow-up accordion-icon accordion-icon-off"></i>
                                            </button>
                                        </h2>
                                        <div id="CustomIconcollapseThree" class="accordion-collapse collapse" aria-labelledby="CustomIconheadingThree" data-bs-parent="#CustomIconaccordionExample">
                                            <div class="accordion-body">
                                                <strong>This is the third item's accordion body.</strong> It is hidden by default, until the collapse
                                                plugin adds the appropriate classes that we use to style each element. These classes control the overall
                                                appearance, as well as the showing and hiding via CSS transitions. You can modify any of this with
                                                custom CSS or overriding our default variables. It's also worth noting that just about any HTML can go
                                                within the <code>.accordion-body</code>, though the transition does limit overflow.
                                            </div>
                                        </div>
                                    </div>
                                </div>
                            </div> <!-- end card-body-->
                        </div> <!-- end card-->
                    </div> <!-- end col-->
                </div>

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

</body>

</html>