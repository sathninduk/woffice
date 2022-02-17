<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }

echo $before_widget;

/*We get the data : */
$user_id = get_current_user_id(); 
$ext_instance = fw()->extensions->get( 'woffice-projects' );
$array_result = $ext_instance->woffice_projects_assigned_tasks($user_id);
$tasks        = $array_result['tasks'];
?>
	<!-- WIDGET -->
	<div class="project-assigned-container">
	
		<div class="project-assigned-head">
            <?php if ($array_result['number'] === 0): ?>
                <i class="fa fa-check-circle  mb-3 fa-4x"></i>
            <?php else: ?>
			    <i class="fa fa-tasks  mb-3 fa-4x"></i>
            <?php endif; ?>
			<div class="intern-box box-title">
				<?php /* the title */
				$task_label = ($array_result['number'] > 1) ? __("tasks","woffice") : __("task","woffice");
				$message = __("You have","woffice"). ' <span class="woffice-colored">'. $array_result['number'] .'</span> '. $task_label;
				?>
				<h3><?php echo $message; ?></h3>
			</div>
		</div>
	
		<?php /* We get the tasks */
		if (!empty($tasks)) { ?>
			<ul class="assigned-tasks-list">
				<?php 
				foreach ($tasks as $task){
					echo '<li class="assigned-task">';
						echo '<a href="'.$task['task_project'].'?#project-content-todo">';
							if (!empty($task['task_date'])) {
								echo '<span class="badge badge-primary badge-pill mr-2">'. date(get_option('date_format'),strtotime(esc_html($task['task_date']))) .'</span>';
							}
							echo $task['task_name'];
						echo'</a>';
					echo '</li>';
				}
				?>
			</ul>
		<?php } else { ?>
			<div class="assigned-tasks-empty">
				<p><?php _e("Well done! You don't have any task from your projects.","woffice"); ?></p>
			</div>
		<?php } ?>
		
	</div>
	
<?php echo $after_widget ?>