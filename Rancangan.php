<html>
<head>
	<title>403 Forbidden</title>
</head>
<body>

<p>Directory access is forbidden.</p>

</body>
</html>

<?php 
/*
***************************************
			U're Late!!
***************************************
##### Database #####
User
- user_id					int				PK
- fb_id						varchar(30)
- username					varchar(30)
- password					varchar(50)
- email						varchar(50)
- phone_number				varchar(20)
- fb_token					varchar(250)
- fb_token_valid_time		datetime

UserGroup
- user_group_id				int				PK
- group_id					int	
- user_id					int
- number_of_late			int
- number_of_miss_schedule	int
- number_of_on_time			int
- number_of_absent			int
- status					boolean (punished or not)

Group 
- group_id					int				PK
- group_name				varchar(30)
- group_description			text
- group_photo				varchar(50)

UserEvent
- user_event_id				int				PK
- event_id					int
- user_id					int
- group_id					int
- status					int (will attend / attend / absent / late / not come)

Event 
- event_id					int				PK
- creator_user_id			int
- group_id					int
- event_name				varchar(30)
- location_name				varchar(50)
- latitude					double
- longitude					double
- event_time				datetime
- cancel_time				datetime

X Log
- log_id					int 			PK
- user_id					int
- group_id					int
- event_id					int
- action					int (will attend / attend / absent / late / not come)

Punishment	(Handled from FB?)
- punishment_id				int				PK
- group_id					int
- user_id					int
- user_point				int
- description				text

Vote
- vote_id					int				PK
- user_id					int
- group_id					int
- punishment_id				int
- status					boolean	(acomplished or not)

### Web Service ###
** User **
V login (username, password)
V renew_token_login (username, password, fb_token, fb_token_valid_time)
V signup (username, user_photo, password, fb_id, email, phone_number, fb_token, fb_token_valid_time)
V get_user_group (user_id)							// Mengembalikan group yang terhubung dengan user id
V get_user_event(user_id)							// Mengembalikan event yang terhubung dengan user id
V get_user_info(user_id)
V search_user(group_id, query)								// Mencari user yang sesuai dengan query dan tidak berada dalam group dengan group_id tertentu

** Group **
V create_group (user_id, group_name, group_description, group_photo)
V edit_group(group_id, group_name, group_description)
V edit_group_photo(group_id, group_photo)
V remove_group(group_ids[])
V add_group_member(group_id, user_ids[])
V leave_group(user_id, group_id)
V remove_group_member(group_id, user_ids[])
V tutup_buku(group_id, punishment)					// Menambahkan semua orang dengan nilai catatan negatif ke Punishment dan menginisialisasi ulang semua number pada UserGroup 
V get_group_users(group_id)							// Mengambil informasi user pada group tertentu
V get_group_events(group_id)
V get_group_info(group_id)

** Event **
V create_event(event_name, group_id, creator_user_id, location_name, latitude, longitude, time, cancel_time)
V edit_event(event_id, event_name, location_name, latitude, longitude, time, cancel_time)
V remove_event(event_id)
V add_attendee(event_id, user_ids[], group_id)
V remove_atendee(event_id, user_ids[])
V close_event(event_id)								// Mengubah status dari semua user pada group tertentu pada UserEvent menjadi not come jika status == will attend
V check_in(user_id, event_id, longitude, latitude)	// Mengubah status dari user dari event tertentu dari will attend menjadi attend
V absent (user_id, event_id)							// Mengubah status dari user dari event tertentu dari will attend menjadi absent
V get_event (event_id)					`			// Mengembalikan event dan info user terhadap event dengan group id tertentu
V get_event_attendee(event_id)						// Mengambil seluruh user pada UserEvent dengan event_id tertentu

** Log **
X get_log (user_id, group_id)
X add_log (user_id, event_id, group_id, action)

** Punishment **
V get_punishment (user_id, group_id, $punishment)
V get_punishment_by_id (punishment_id)
V finish_punishment (user_id, group_id)		// Menyelesaikan punishment yang belum selesai, akan mengemail ke seluruh anggota group sebagai penilai
V delete_punishment (punishment_id)

** Vote **
V vote_user(user_id, punishment_id, status)						// Mengubah status vote dengan vote_id tertentu, dan menghapus punishment jika jumlah vote sudah melebihi batas tertentu

### Windows 8 ###
Use Case :
1. Login
2. Sign Up
3. See Group (Description + Event + Member Statistic)
4. See Event (Member Atending status)
5. See User Log	(See user log in group)

Screen :
1. Login Screen		-> login(), sign_up()
2. Group List Screen	->	get_group()
3. Group Detail Screen 	-> Group Info, Member Statistic List	-> get_user_group_info()
4. User Detail Screen -> get_log(), get_user()

### Windows Phone 8 ###
Use Case :
1. Login
2. Sign Up
3. See Group (Description + Event + Member Statistic)
4. See Event (Member Atending status)
5. See User Log	(See user log in group)
6. Sign Absent
7. Close Absent
8. Tutup Buku
9. Event Reminder
10. Pegang Absen

Screen :
1. Login Screen		-> login(), sign_up()
2. Group List Screen	->	get_group(), remove_group(), create_group()
3. Group Detail Screen 	-> 	Group Info, Member List, Event List	-> get_event(), create_event(), edit_event(), remove_event(), leave_group(), remove_member(), add_member(), tutup_buku()
4. Event Detail Screen 	->	get_event_atendee(), add_atendee(), remove_atendee(), absen(), close_event(), AddReminder, RemoveReminder, EditReminder
5. User Detail Screen 	->	get_log(), get_user()

6. Add Reminder Screen
7. Edit Reminder Screen
8. Add Group Screen
9. Edit Group Screen
10. Add Event Screen
11. Edit Event Screen
12. Add Member Screen
13. Add Attendee Screen

### Facebook Apps ###
Use Case :
1. Auto Post
2. Update Punishment State

### Twitter Apps ###
Use Case :
1. Auto Post
2. Update Punishment State

### Constant ###
Event Status / Log Action
0 = will attend 
1 = attend 
2 = absent 
3 = late 
4 = not come

Vote Status
0 = not acomplished
1 = acomplished

User Group Status
0 = not punished
1 = punished

### Job Desc Sampe Awal Agustus ###
Mumu -> Mock Up Windows 8
Gilang, Agung -> Mock Up WP8
Samuel, Regie -> Backend
Gilang -> Twitter
Samuel -> FB
*/
?>
