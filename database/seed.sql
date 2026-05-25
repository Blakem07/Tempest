INSERT INTO projects
(id, title, description, manager, location_name, latitude, longitude)
VALUES
(
    1,
    'NESST',
    'A new university building with lab spaces, meeting rooms, breakout areas, kitchen areas and WC facilities.',
    'Chelsea Dawson',
    'Northumbria University, Ellison Terrace, Newcastle upon Tyne, NE1 8ST',
    54.97641468,
    -1.60663669
),
(
    2,
    'CHASE',
    'A new university building with lab spaces, meeting rooms, breakout areas, kitchen areas and WC facilities.',
    'Peter Duncan',
    'Northumbria University, Ellison Terrace, Newcastle upon Tyne, NE1 8ST',
    54.97919158,
    -1.60648639
),
(
    3,
    'HMRC',
    'An office space for a public sector client to include gym space, staff rooms with kitchen areas, toilet facilities, meeting rooms and breakout areas.',
    'Dan Smith',
    'New Bridge Street, Newcastle upon Tyne, NE1 2SW',
    54.97419180,
    -1.61130369
),
(
    4,
    'St James Park',
    'An extension to the existing football stadium to include a clubhouse for coaching non-professional players and hosting events. To include a small field, an exhibition room, toilet facilities and a kitchen.',
    'Chelsea Dawson',
    'Newcastle United Football Co Ltd, St. James Park, Strawberry Place, Newcastle upon Tyne, NE1 4ST',
    54.97470900,
    -1.62047673
);

INSERT INTO resources
(id, name, resource_type, conditions_of_use)
VALUES
(
    1,
    'Crane',
    'Lifting equipment',
    'Do not use in high wind'
),
(
    2,
    'Drill',
    'Drilling equipment',
    'Do not use in heavy rain'
),
(
    3,
    'Dumper Truck',
    'Earth-moving equipment',
    'Do not use in heavy rain. Has CO2 emissions so do not use if air quality CO, PM10, PM2.5 or NO2 readings are moderate or poorer.'
),
(
    4,
    'Digger',
    'Earth-moving equipment',
    'Do not use in heavy rain. Has CO2 emissions so do not use if air quality CO, PM10, PM2.5 or NO2 readings are moderate or poorer.'
),
(
    5,
    'Loader',
    'Earth-moving equipment',
    'Do not use in heavy rain. Has CO2 emissions so do not use if air quality CO, PM10, PM2.5 or NO2 readings are moderate or poorer.'
),
(
    6,
    'Concrete mixer',
    'Concrete equipment',
    'Do not use in heavy rain'
);

INSERT INTO project_resources
(project_id, resource_id)
VALUES
(1, 1),
(1, 2),
(1, 3),
(1, 4),
(1, 5),

(2, 1),
(2, 2),
(2, 3),
(2, 4),
(2, 5),

(3, 1),
(3, 3),
(3, 4),
(3, 5),
(3, 6),

(4, 1),
(4, 3),
(4, 5),
(4, 6);