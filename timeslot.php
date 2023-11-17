<?php
    class TimeSlot {
        /* Represents a time slot.
        Properties:
            id: int - Unique identifier for the time slot
            day: string - Day (e.g. 18/09/2023)
            start_time: string - Start time (e.g. 10:00)
            end_time: string - End time (e.g. 11:00)
            is_booked: bool - Whether the time slot is booked
            assigned_to: int - User ID of the user assigned to the time slot
        Methods:
            book($user_id) - Book the time slot for the user with the given ID
            unbook() - Unbook the time slot
        */

        public $id;
        public $day;
        public $start_time;
        public $end_time;
        public $is_booked;
        public $assigned_to;
        public $color;

        function __construct($id, $day, $start_time, $end_time, $color, $is_booked = false, $assigned_to = null) {
            $this->id = $id;
            $this->day = $day;
            $this->start_time = $start_time;
            $this->end_time = $end_time;
            $this->color = $color;
            $this->is_booked = $is_booked;
            $this->assigned_to = $assigned_to;
        }

        function book($user_id) {
            $this->is_booked = true;
            $this->assigned_to = $user_id;
        }

        function unbook() {
            $this->is_booked = false;
            $this->assigned_to = null;
        }

        function __toString() {
            return $this->day . " " . $this->start_time . " - " . $this->end_time;
        }

        function __toArray() {
            return array(
                "id" => $this->id,
                "day" => $this->day,
                "start_time" => $this->start_time,
                "end_time" => $this->end_time,
                "color" => $this->color,
                "is_booked" => $this->is_booked,
                "assigned_to" => $this->assigned_to
            );
        }
    }
?>