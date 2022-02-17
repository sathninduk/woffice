<?php if ( ! defined( 'ABSPATH' ) ) { die( 'Direct access forbidden.' ); }

echo $before_widget;

/*We get the data : */
$user_id = get_current_user_id(); 
$ext_instance = fw()->extensions->get( 'woffice-projects' );
$array_result = $ext_instance->woffice_projects_assigned_tasks_full_list($user_id);
$tasks        = $array_result['tasks'];
?>
	<!-- WIDGET -->
	<div class="project-assigned-container assigned_<?php echo $style_type;?>">
	
		<div class="project-assigned-head">
           
		   <div class="intern-box box-title">
				<?php /* the title */
					$message = __($title,"woffice"). ' <span class="woffice-colored">('. $array_result['number'] .')</span> ';
				?>
				<h3><?php echo $message; ?></h3>
			</div>
		</div>
	
		<?php /* We get the tasks */
		if (!empty($tasks)) { ?>
			<ul class="assigned-tasks-list">
				<?php 
				foreach ($tasks as $task){

					if(isset($task['task_done']) && $task['task_done'] == true){
						echo '<del><li class="assigned-task assigned-task-sytle-2">';
							echo '<a href="'.$task['task_project'].'?#project-content-todo">';
								echo $task['task_name'];
							echo'</a>';
								echo '<span class="fa fa-check-circle is-done"></span>';
						echo '</li></del>';
					} else {
						echo '<li class="assigned-task assigned-task-sytle-2">';
							echo '<a href="'.$task['task_project'].'?#project-content-todo">';
								echo $task['task_name'];
							echo'</a>';
								echo '<span class="fa fa-check-circle-o"></span>';
						echo '</li>';
					}
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