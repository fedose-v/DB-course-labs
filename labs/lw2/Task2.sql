-- 1
-- up
UPDATE flights
SET
	`status` = 'Cancelled'
WHERE `status` = 'Delayed' 
	AND (departure_airport = 'DME' OR arrival_airport = 'DME')
;

-- 2
-- up
UPDATE flights f
	INNER JOIN airports_data ad ON (
		f.departure_airport = ad.airport_code
        OR f.arrival_airport = ad.airport_code
    )
SET
	f.status = 'Arrived',
    f.actual_departure = f.scheduled_departure,
    f.actual_arrival = f.scheduled_arrival
WHERE airport_name->>'$.ru' = 'Йошкар-Ола'
	AND f.status = 'Scheduled'
;

-- 3
-- up
DELETE 
    bp.*
FROM tickets t
    INNER JOIN ticket_flights tf ON tf.ticket_no = t.ticket_no
    INNER JOIN flights f ON f.flight_id = tf.flight_id
    INNER JOIN boarding_passes bp ON (
		tf.ticket_no = bp.ticket_no
        AND tf.flight_id = bp.flight_id
    )
WHERE t.passenger_name = 'GENNADIY NIKITIN'
;

DELETE 
    tf.*
FROM tickets t
    INNER JOIN ticket_flights tf ON tf.ticket_no = t.ticket_no
    INNER JOIN flights f ON f.flight_id = tf.flight_id
WHERE t.passenger_name = 'GENNADIY NIKITIN'
;

-- delete bookings without tickets
DELETE 
    t.*
FROM tickets t
WHERE t.passenger_name = 'GENNADIY NIKITIN'
;




-- SELECT DISTINCT
-- 	f.*
-- FROM tickets t
--     INNER JOIN ticket_flights tf ON tf.ticket_no = t.ticket_no
--     INNER JOIN flights f ON f.flight_id = tf.flight_id
--     INNER JOIN boarding_passes bp ON (
-- 		tf.ticket_no = bp.ticket_no
--         AND tf.flight_id = bp.flight_id
--     )
-- WHERE t.passenger_name = 'GENNADIY NIKITIN'
-- ;
