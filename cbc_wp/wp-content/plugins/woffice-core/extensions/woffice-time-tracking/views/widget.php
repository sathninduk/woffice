<?php

// Let's make things easier
$ext_instance = fw()->extensions->get( 'woffice-time-tracking' );

$log = $ext_instance->getLog(get_current_user_id());

$class = (woffice_tracking_is_working()) ? 'is-tracking' : '';

echo $before_widget; ?>

<!-- WIDGET -->
<div class="woffice-time-tracking <?php echo $class; ?>">

    <?php if(!is_user_logged_in()) : ?>

        <div class="woffice-time-tracking-head">
            <i class="fa fa-lock"></i>
            <div class="intern-box box-title">
                <h3><?php _e('Sorry ! It is only for logged users.','woffice'); ?></h3>
            </div>
        </div>

    <?php else: ?>

        <div class="woffice-time-tracking-head">
            <i class="fa fa-clock"></i>
            <?php if(!empty($title)) : ?>
                <div class="intern-box box-title">
                    <h3><?php echo $title; ?></h3>
                </div>
            <?php endif; ?>
        </div>

        <div class="woffice-time-tracking-content">
            <div class="woffice-time-tracking-view text-center">
                <p><?php echo $description; ?></p>
                <div class="woffice-time-tracking_time-displayed"><?php echo woffice_current_tracking_time(); ?></div>
            </div>
            <div class="woffice-time-tracking-view text-left" style="display: none;">
                <?php if(!empty($log)) : ?>
                    <?php foreach ($log as $day=>$entry) : ?>
                        <div class="woffice-time-tracking-day"
                             data-toggle="popover"
                             data-placement="top"
                             data-content="<?php echo esc_html($entry['meta']); ?>">
                            <span class="badge badge-primary badge-pill" data-tracking-day="<?php echo $day; ?>"><?php echo $day; ?></span>
                            <span data-tracking-hours="<?php echo $entry['total']; ?>">
                                <?php echo $entry['total']; ?>
                                <?php if ($entry['meta']) : ?>
                                    <i class="fa fa-external-link-alt text-light"></i>
                                <?php endif; ?>
                            </span>
                        </div>
                    <?php endforeach; ?>
                <?php else: ?>
                    <p><?php _e('No tracks so far. Get started!', 'woffice'); ?></p>
                <?php endif; ?>
            </div>
        </div>

        <div class="woffice-time-tracking-actions text-center">
            <a href="#" class="woffice-time-tracking-history-toggle btn btn-default btn-sm">
                <i class="fa-history fa"></i> <?php _e('Tracks', 'woffice'); ?>
            </a>
            <a href="#"
               data-action="modal"
               class="woffice-time-tracking-state-toggle btn btn-default btn-info btn-sm start <?php echo esc_attr(woffice_tracking_is_working() ? "d-none" : "")?>">
                <i class="fa fa-play"></i> <span><?php _e('Start', 'woffice'); ?></span>
            </a>

            <a href="#"
               data-action="stop"
               class="woffice-time-tracking-state-toggle btn btn-default btn-danger btn-sm stop <?php echo esc_attr(woffice_tracking_is_working() ? "" : "d-none")?>">
                <i class="fa fa-stop"></i> <span><?php _e('Stop', 'woffice'); ?></span>
            </a>
        </div>

    <?php endif; ?>

</div>

<?php echo $after_widget; ?>