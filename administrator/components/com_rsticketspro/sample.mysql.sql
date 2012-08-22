INSERT IGNORE INTO `#__rsticketspro_departments` (`id`, `name`, `prefix`, `assignment_type`, `generation_rule`, `next_number`, `customer_send_email`, `staff_send_email`, `upload`, `upload_extensions`, `notify_new_tickets_to`, `notify_assign`, `priority_id`, `published`, `ordering`) VALUES
(1, 'Billing', 'BILLING', 1, 0, 1, 1, 1, 0, '', '', 1, 1, 1, 1),
(2, 'Licensing', 'LICENSE', 1, 0, 1, 0, 1, 0, '', '', 1, 3, 1, 3),
(3, 'Tech Support', 'TECH', 1, 0, 1, 1, 1, 1, 'zip\r\njpg', '', 1, 1, 1, 2),
(4, 'Pre Sales', 'PRE', 0, 1, 1, 1, 1, 0, '', '', 0, 1, 1, 4);

INSERT IGNORE INTO `#__rsticketspro_groups` (`id`, `name`, `add_ticket`, `add_ticket_customers`, `add_ticket_staff`, `update_ticket`, `update_ticket_custom_fields`, `delete_ticket`, `answer_ticket`, `update_ticket_replies`, `update_ticket_replies_customers`, `update_ticket_replies_staff`, `delete_ticket_replies_customers`, `delete_ticket_replies_staff`, `delete_ticket_replies`, `assign_tickets`, `change_ticket_status`, `see_unassigned_tickets`, `see_other_tickets`, `move_ticket`, `view_notes`, `add_note`, `update_note`, `update_note_staff`, `delete_note`, `delete_note_staff`) VALUES
(1, 'Staff', 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1, 1);