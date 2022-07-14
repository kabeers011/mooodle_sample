<?php

defined('MOODLE_INTERNAL') || die;

/**
 * extending the navigation with the report items
 *
 */
function kb_report_completion_extend_navigation_course($navigation, $course, $context) {
    global $CFG;

    require_once($CFG->libdir.'/completionlib.php');

    if (has_capability('report/completion:view', $context)) {


        $completion = new completion_info($course);

        if ($completion->is_enabled() && $completion->has_criteria()) {

            $url = new moodle_url('/report/completion/index.php', array('course'=>$course->id));

            $navigation->add(get_string('pluginname','report_completion'), $url, navigation_node::TYPE_SETTING, null, null, new pix_icon('i/report', ''));
        }
    }
}

/**
 * extending the courses navigation with the report items
 *
 */
function kb_report_completion_extend_navigation_user($navigation, $user, $course) {

    if (report_completion_can_access_user_report($user, $course)) {
      
        $url = new moodle_url('/report/completion/user.php', array('id'=>$user->id, 'course'=>$course->id));
       
        $navigation->add(get_string('coursecompletion'), $url);
    }
}

/**
 * Is current user allowed to access this report
 *
 */
function kb_report_completion_can_access_user_report($user, $course) {

    global $USER, $CFG;

    if (empty($CFG->enablecompletion)) {
        return false;
    }

    if ($course->id != SITEID and !$course->enablecompletion) {
        return false;
    }

    $coursecontext = context_course::instance($course->id);
    
    $personalcontext = context_user::instance($user->id);

    if ($user->id == $USER->id) {
       
        if ($course->showreports and (is_viewing($coursecontext, $USER) or is_enrolled($coursecontext, $USER))) {
            return true;
        }
    } else if (has_capability('moodle/user:viewuseractivitiesreport', $personalcontext)) {
      
        if ($course->showreports and (is_viewing($coursecontext, $user) or is_enrolled($coursecontext, $user))) {
            return true;
        }

    }

    if (!groups_user_groups_visible($course, $user->id)) {
      
        return false;

    }

    if (has_capability('report/completion:view', $coursecontext)) {
      
        return true;

    }

    return false;
}

