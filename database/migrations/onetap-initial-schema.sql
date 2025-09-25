-- ============================================================
-- OneTAP: Initial Schema
-- File: onetap-initial-schema.sql
-- Purpose: Full baseline schema snapshot for reference.
-- Note: This file is NOT auto-run by Laravel. 
--       Use migrations for incremental updates.
-- ============================================================

users {
	id integer pk increments unique
	first_name varchar
	middle_name varchar null
	last_name varchar
	sex char null def(NULL)
	city varchar
	town varchar
	province varchar
	email varchar unique
	password varchar
	created_at timestamp
	updated_at timestamp
}

roles {
	id integer pk increments unique
	name varchar
	description varchar null
	created_at timestamp
	updated_at timestamp
}

role_user {
	id integer pk increments unique
	user_id integer > users.id
	role_id integer > roles.id
	created_at timestamp
	updated_at timestamp
}

programs {
	id integer pk increments unique
	name varchar
	description varchar null
	created_at timestamp
	updated_at timestamp
}

classrooms {
	id integer pk increments unique
	program_id integer > programs.id
	year_level varchar
	section varchar null
	created_at timestamp
	updated_at timestamp
}

courses {
	id integer pk increments unique
	name varchar
	description varchar
	code varchar
	created_at timestamp
	updated_at timestamp
}

instructors {
	id integer pk increments unique
	user_id integer > users.id
	program_id integer null > programs.id
	created_at timestamp
	updated_at timestamp
}

students {
	id integer pk increments unique
	user_id integer > users.id
	program_id integer > programs.id
	created_at timestamp
	updated_at timestamp
}

class_courses {
	id integer pk increments unique
	class_id integer > classrooms.id
	course_id integer > courses.id
	instructor_id integer > instructors.id
	semester varchar
	academic_year varchar
	created_at timestamp
	updated_at timestamp
}

enrollments {
	id integer pk increments unique
	class_course_id integer > class_courses.id
	student_id integer > students.id
	created_at timestamp
	updated_at timestamp
}

announcements {
	id integer pk increments unique
	created_by integer > users.id
	title varchar
	description varchar null
	event_date date null
	event_time time null
	created_at timestamp
	updated_at timestamp
}

announcement_targets {
	id integer pk increments unique
	announcement_id integer > announcements.id
	target_type enum('global', 'role', 'program', 'classroom', 'course')
	target_id integer null
	created_at timestamp
	updated_at timestamp
}

user_pinned_announcements {
	id integer pk increments unique
	announcement_id integer > announcements.id
	user_id integer > users.id
	created_at timestamp
}

tasks {
	id integer pk increments unique
	class_course_id integer > class_courses.id
	title varchar
	description varchar
	due_date date
	due_time time null
	category enum('assignment', 'project', 'quiz', 'exam', 'activity', 'other')
	created_at timestamp
	updated_at timestamp
}

task_statuses {
	id integer pk increments unique
	task_id integer > tasks.id
	student_id integer > students.id
	is_finished boolean def(FALSE)
	finished_at datetime null
	created_at timestamp
	updated_at timestamp
}

submissions {
	id integer pk increments unique
	task_id integer > tasks.id
	student_id integer > students.id
	file_url varchar null
	text_submission varchar null
	submitted_at datetime
	created_at timestamp
	updated_at timestamp
}

grades {
	id integer pk increments unique
	submission_id integer > submissions.id
	grade integer null def(0)
	out_of integer null def(0)
	created_at timestamp
	updated_at timestamp
}

calendar_schedules {
	id integer pk increments unique
	title varchar
	description varchar null
	schedule_type enum('general', 'class', 'course', 'task', 'exam', 'meeting')
	related_id bigint null
	start_datetime datetime
	end_datetime datetime null
	is_all_day boolean def(FALSE)
	created_by integer > users.id
	created_at timestamp
	updated_at timestamp
}

calendar_schedule_targets {
	id integer pk increments unique
	calendar_schedule_id integer > calendar_schedules.id
	target_type enum('global', 'role', 'program', 'classroom', 'course')
	target_id integer null
	created_at timestamp
	updated_at timestamp
}

class_sessions {
	id integer pk increments unique
	class_course_id integer > class_courses.id
	start_datetime datetime
	end_datetime datetime
	status enum('scheduled', 'ongoing', 'completed', 'cancelled') def(scheduled)
	topic varchar null
	created_at timestamp
	updated_at timestamp
}

attendance_records {
	id integer pk increments unique
	student_id integer > students.id
	class_session_id integer > class_sessions.id
	status enum('present', 'late', 'absent', 'excused', 'suspended')
	time_in time null
	remarks varchar null
	marked_by integer > users.id
	created_at timestamp
	updated_at timestamp
}

notifications {
	id integer pk increments unique
	user_id integer > users.id
	title varchar
	message varchar
	type enum('system', 'announcement', 'task', 'calendar', 'attendance', 'grade', 'message')
	is_read boolean def(FALSE)
	created_at timestamp
	updated_at timestamp
}