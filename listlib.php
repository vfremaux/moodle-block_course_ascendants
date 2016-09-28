<?php
// This file is part of Moodle - http://moodle.org/
//
// Moodle is free software: you can redistribute it and/or modify
// it under the terms of the GNU General Public License as published by
// the Free Software Foundation, either version 3 of the License, or
// (at your option) any later version.
//
// Moodle is distributed in the hope that it will be useful,
// but WITHOUT ANY WARRANTY; without even the implied warranty of
// MERCHANTABILITY or FITNESS FOR A PARTICULAR PURPOSE.  See the
// GNU General Public License for more details.
//
// You should have received a copy of the GNU General Public License
// along with Moodle.  If not, see <http://www.gnu.org/licenses/>.

defined('MOODLE_INTERNAL') || die();

/**
 * Version details
 *
 * @package    block_course_ascendants
 * @category   blocks
 * @copyright  2012 onwards Valery Fremaux (valery.fremaux@gmail.com)
 * @license    http://www.gnu.org/copyleft/gpl.html GNU GPL v3 or later
 */

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