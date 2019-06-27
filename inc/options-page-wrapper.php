
<!-- <h2><?php esc_attr_e( '2 Columns Layout: static (px)', 'WpAdminStyle' ); ?></h2> -->

<div class="wrap">

	<div id="icon-options-general" class="icon32"></div>
	<h1>The Official Treehouse Badges Plugin</h1>

	<div id="poststuff">

		<div id="post-body" class="metabox-holder columns-2">

			<!-- main content -->
			<div id="post-body-content">

				<div class="meta-box-sortables ui-sortable">
					<?php if(!isset($wptreehouse_username) || $wptreehouse_username == '') : ?>

					<div class="postbox">
							<button type="button" class="handlediv" aria-expanded="true" >
								<span class="screen-reader-text">Toggle panel</span>
								<span class="toggle-indicator" aria-hidden="true"></span>
							</button>
							<!-- Toggle -->
							<h2 class="hndle"><span>Let's Get Started!</span></h2>
							<div class="inside">
								<form name= "wptreehouse_username_form" method="post" action="">
									<input type= "hidden" name="wptreehouse_form_submitted" value="Y">
									<table class="form-table">
										<tr>
											<td>
												<label for="wptreehouse_username">Treehouse username</label>
											</td>
											<td>
												<input name = "wptreehouse_username" id="wptreehouse_username" type="text" value="" class="regular-text" />
											</td>
										</tr>
									</table>
									<p>
										<input class="button-primary" type="submit" name="wptreehouse_username-submit" value="Save" />
									</p>
								</form>
							</div><!-- .inside -->
					</div><!-- .postbox -->
						<?php else: ?>
							<div class="postbox">

								<button type="button" class="handlediv" aria-expanded="true" >
									<span class="screen-reader-text">Toggle panel</span>
									<span class="toggle-indicator" aria-hidden="true"></span>
								</button>
								<!-- Toggle -->

								<h2 class="hndle"><span>Most Recent Badges</span>
								</h2>
								<div class="inside">
									<p>Below are your 20 most recent badges</p>
									<ul class="wptreehouse-badges">
									<?php 
										$total_badges = count( $wptreehouse_profile->{'badges'} );
										for( $i = $total_badges - 1; $i >= $total_badges - 20; $i-- ):		
										;?>
											<li>
												<ul>
													<li>	
														<img width="120px" src="<?php echo $wptreehouse_profile->{'badges'}[$i]->{'icon_url'}; ?>">								
													</li>
													<?php if ( $wptreehouse_profile->{'badges'}[$i]->{'url'} != $wptreehouse_profile->{'profile_url'} ) : ?>									
														<li class="wptreehouse-badge-name">

															<a href="<?php echo $wptreehouse_profile->{'badges'}[$i]->{'courses'}[1]->{'url'}; ?>">
																<?php echo $wptreehouse_profile->{'badges'}[$i]->{'name'}; ?>
															</a>
														</li>
														<li class="wptreehouse-project-name">
															<a href="<?php echo $wptreehouse_profile->{'badges'}[$i]->{'courses'}[0]->{'url'}; ?>">
																<?php echo $wptreehouse_profile->{'badges'}[$i]->{'courses'}[0]->{'title'}; ?>
															</a>
														</li>
													<?php else: ?>
													<li class="wptreehouse-badge-name">
															<?php echo $wptreehouse_profile->{'badges'}[$i]->{'name'}; ?>
													</li>
													<?php endif; ?>														
												</ul>									
											</li>								
										<?php endfor; ?>
									</ul>
								</div>
								<!-- .inside -->
						<?php endif; ?>
					</div><!-- .postbox -->
					<?php if ( $display_json == true ) : ?>
						<div class="postbox">
							<h2 class="hndle"><span>JSON Feed</span></h2>
							<div class="inside">
								<p>
									<?php echo $wptreehouse_profile->{'name'}; ?>
								</p>
								<p>
									<?php echo $wptreehouse_profile->{'profile_url'}; ?>
								</p>
								<p>
									<?php echo $wptreehouse_profile->{'badges'}[0]->{'name'}; ?>
								</p>		
								<p>
									<?php echo $wptreehouse_profile->{'badges'}[1]->{'courses'}[1]->{'title'}; ?>
								</p>				
								<pre><code>
									<?php var_dump( $wptreehouse_profile ); ?>
								</code></pre>
							</div>
						</div>
					<?php endif; ?>
				</div>
				<!-- .meta-box-sortables .ui-sortable -->

			</div>
			<!-- post-body-content -->

			<!-- sidebar -->
			<div id="postbox-container-1" class="postbox-container">

				<div class="meta-box-sortables">
					<?php if(isset($wptreehouse_username) && $wptreehouse_username != '') : ?>
					<div class="postbox">
							<h3><span><?php echo $wptreehouse_profile->{'name'}; ?>'s Profile</span></h3>
							<div class="inside">						
							<p><img width="100%" class="wptreehouse-gravatar" src="<?php echo $wptreehouse_profile->{'gravatar_url'}; ?>" alt="Mike the Frog Gravatar"></p>
								<ul class="wptreehouse-badges-and-points">							
									<li>Badges: <strong><?php echo count($wptreehouse_profile->{'badges'}); ?></strong></li>
									<li>Points: <strong><?php echo $wptreehouse_profile->{'points'}->{'total'}; ?></strong></li>
								</ul>
								<form name= "wptreehouse_username_form" method="post" action="">
									<input type= "hidden" name="wptreehouse_form_submitted" value="Y">
									<p>
										<label for="wptreehouse_username">Username</label>
									</p>
									<p>
										<input name = "wptreehouse_username" id="wptreehouse_username" type="text" value="<?php echo $wptreehouse_username ?>"/>
										<input class="button-primary" type="submit" name="wptreehouse_username-submit" value="Update" />
									</p>
								</form>
							</div> <!-- .inside -->

					</div><!-- .postbox -->
					<?php endif; ?>
				</div>
				<!-- .meta-box-sortables -->

			</div>
			<!-- #postbox-container-1 .postbox-container -->

		</div>
		<!-- #post-body .metabox-holder .columns-2 -->

		<br class="clear">
	</div>
	<!-- #poststuff -->

</div> <!-- .wrap -->