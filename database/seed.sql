-- Tempest Phase 2 seed data
-- Sample project and equipment records for development and testing.

-- Insert sample construction projects with location coordinates.
INSERT INTO projects
(title, description, location_name, latitude, longitude, start_date, end_date)
VALUES
(
    'Central Bridge Demolition',
    'Demolition and clearance of an ageing road bridge before replacement works begin.',
    'Newcastle upon Tyne',
    54.9783000,
    -1.6178000,
    '2026-05-01',
    '2026-08-15'
),
(
    'Riverside Apartment Build',
    'Construction of a medium-rise residential apartment block near the riverside.',
    'Gateshead',
    54.9626000,
    -1.6015000,
    '2026-06-03',
    '2027-02-28'
),
(
    'South Yard Groundworks',
    'Groundworks, excavation and site preparation for an industrial storage facility.',
    'Sunderland',
    54.9069000,
    -1.3838000,
    '2026-04-20',
    '2026-07-30'
);

-- Insert equipment resources used for risk recommendation logic.
INSERT INTO resources
(name, resource_type)
VALUES
('Crane', 'Lifting equipment'),
('Digger', 'Earth-moving equipment'),
('Dumper truck', 'Earth-moving equipment'),
('Concrete mixer', 'Concrete equipment'),
('Scaffolding', 'Access equipment'),
('Generator', 'Power equipment');

-- Assign equipment resources to projects with quantities.
INSERT INTO project_resources
(project_id, resource_id, quantity)
VALUES
(1, 1, 1),
(1, 2, 2),
(1, 3, 2),
(1, 6, 1),
(2, 1, 1),
(2, 4, 2),
(2, 5, 1),
(2, 6, 1),
(3, 2, 3),
(3, 3, 4),
(3, 6, 2);