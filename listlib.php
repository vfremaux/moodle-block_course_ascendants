<?php

function list_up($blockid, $courseid) {
    global $DB;

    $item = $DB->get_record('block_course_ascendants', array('blockid' => $blockid, 'courseid' => $courseid));
    if (!$nextitem = $DB->get_record('block_course_ascendants', array('blockid' => $item->blockid, 'sortorder' => $item->sortorder + 1))) {
        return;
    }
    $nextitem->sortorder--;
    $item->sortorder++;
    $DB->update_record('block_course_ascendants', $item);
    $DB->update_record('block_course_ascendants', $nextitem);
}

function list_down($blockid, $courseid) {
    global $DB;

    $item = $DB->get_record('block_course_ascendants', array('blockid' => $blockid, 'courseid' => $courseid));
    if ($item->sortorder == 0) {
        return;
    }
    $previtem = $DB->get_record('block_course_ascendants', array('blockid' => $item->blockid, 'sortorder' => $item->sortorder - 1));
    $previtem->sortorder++;
    $item->sortorder--;
    $DB->update_record('block_course_ascendants', $item);
    $DB->update_record('block_course_ascendants', $previtem);
}

function list_last_order($blockid) {
    global $DB;

    $lastorder = $DB->get_field('block_course_ascendants', 'MAX(sortorder)', array('blockid' => $blockid));
    return $lastorder;
}

function list_remove($blockid, $courseid) {
    global $DB;

    $oldorder = $DB->get_field('block_course_ascendants', 'sortorder', array('blockid' => $blockid, 'courseid' => $courseid));
    $DB->delete_records('block_course_ascendants', array('blockid' => $blockid, 'courseid' => $courseid));
    $sql = "
        UPDATE
            {block_course_ascendants} bas
        SET
            sortorder = sortorder - 1
        WHERE
            blockid = ? AND
            sortorder > ?
    ";
    $DB->execute($sql, array($blockid, $oldorder));
}