<?php if (!empty($title)) : ?>
    <div class="page-title-head d-flex align-items-center">
        <div class="flex-grow-1">
            <h4 class="fs-xl fw-bold m-0"><?php echo ($title); ?></h4>
        </div>

        <div class="text-end">
            <ol class="breadcrumb m-0 py-0">
                <li class="breadcrumb-item"><a href="javascript: void(0);">Go Muni</a></li>
                <?php if (!empty($subtitle)) : ?>
                    <li class="breadcrumb-item"><a href="javascript: void(0);"><?php echo ($subtitle); ?></a></li>
                <?php endif ?>
                <li class="breadcrumb-item active"><?php echo ($title); ?></li>
            </ol>
        </div>
    </div>
<?php endif ?>