<?php
// Last Modified Date: 12-04-2024
// Developed By: Innovative Solution Pvt. Ltd. (ISPL)  
return [
"insert_road_and_create_topology" => [

        "fnc_set_insert_road_and_create_topology" => "
            CREATE OR REPLACE FUNCTION fnc_set_insert_road_and_create_topology()
            RETURNS TRIGGER LANGUAGE plpgsql
            AS $$
            BEGIN
                -- Step 1: Create a working table for noding
                CREATE TABLE IF NOT EXISTS utility_info.roads_network_noded (
                    id BIGSERIAL PRIMARY KEY,
                    old_id INTEGER,
                    sub_id INTEGER,
                    source BIGINT,
                    target BIGINT,
                    the_geom GEOMETRY(LineString, 4326),
                    distance DOUBLE PRECISION
                ) TABLESPACE pg_default;

                -- Step 2: Insert noded segments from original roads
                INSERT INTO utility_info.roads_network_noded (the_geom)
                SELECT (ST_Dump(geom)).geom
                FROM utility_info.roads;

                -- Step 3: Create topology
                PERFORM pgr_createTopology(
                    'utility_info.roads_network_noded',
                    0.00001,
                    'the_geom',
                    'id'
                );

                -- Step 4: Node the network
                PERFORM pgr_nodeNetwork(
                    'utility_info.roads_network_noded',
                    0.00001,
                    the_geom := 'the_geom'
                );

                -- Step 5: Drop old output table if exists
                DROP TABLE IF EXISTS utility_info.roads_network_noded;

                -- Step 6: Rename noded output to final table
                ALTER TABLE utility_info.roads_network_noded_noded
                RENAME TO roads_network_noded;

                -- Step 7: Rename primary key constraint
              ALTER INDEX utility_info.roads_network_noded_noded_the_geom_idx
                RENAME TO roads_network_noded_the_geom_idx;

                -- Step 8: Recreate topology on the renamed table
                PERFORM pgr_createTopology(
                    'utility_info.roads_network_noded',
                    0.00001,
                    'the_geom',
                    'id'
                );

                -- Step 9: Add distance column and update it
                ALTER TABLE utility_info.roads_network_noded
                ADD COLUMN IF NOT EXISTS distance DOUBLE PRECISION;

                UPDATE utility_info.roads_network_noded
                SET distance = ST_Length(the_geom::geography);
             RETURN NULL;
            END $$;",

            "tgr_set_insert_road_and_create_topology"=>"DROP TRIGGER IF EXISTS tgr_set_insert_road_and_create_topology ON utility_info.roads;  
            CREATE TRIGGER tgr_set_insert_road_and_create_topology
            AFTER INSERT OR DELETE OR UPDATE
            ON utility_info.roads   		
            FOR EACH ROW
            EXECUTE PROCEDURE fnc_set_insert_road_and_create_topology();",
    ],


    // function to get point buffer buildings 
    "fnc_getPointBufferBuildings" => [

        "fnc_drop_getPointBufferBuildings" => "DROP FUNCTION IF EXISTS fnc_getPointBufferBuildings(DOUBLE PRECISION, DOUBLE PRECISION, integer);",

        "fnc_set_getPointBufferBuildings" => "
                CREATE OR REPLACE FUNCTION fnc_getPointBufferBuildings(_param_long DOUBLE PRECISION, _param_lat DOUBLE PRECISION, _param_distance integer)
                RETURNS table (structype varchar(254), count integer, sewer_network integer, drain_network integer, septic_tank integer, pit_holding_tank integer,
                onsite_treatment integer, composting_toilet integer, water_body integer, open_ground integer, community_toilet integer,
                open_defacation integer)  
                LANGUAGE plpgsql AS $$
                
                BEGIN
                    RETURN Query
                    SELECT st.type, COUNT(*)::integer AS count,
                        COUNT(b.bin) filter (where b.sanitation_system_id = '1')::integer  AS sewer_network,
                        COUNT(b.bin) filter (where b.sanitation_system_id = '2')::integer  AS drain_network,
                        COUNT(b.bin) filter (where b.sanitation_system_id = '3')::integer AS septic_tank,
                        COUNT(b.bin) filter (where b.sanitation_system_id = '4')::integer AS pit_holding_tank,
                        COUNT(b.bin) filter (where b.sanitation_system_id = '5')::integer AS onsite_treatment,
                        COUNT(b.bin) filter (where b.sanitation_system_id = '6')::integer AS composting_toilet,
                        COUNT(b.bin) filter (where b.sanitation_system_id = '7')::integer AS water_body,
                        COUNT(b.bin) filter (where b.sanitation_system_id = '8')::integer AS open_ground,
                        COUNT(b.bin) filter (where b.sanitation_system_id = '9')::integer AS community_toilet,
                        COUNT(b.bin) filter (where b.sanitation_system_id = '10')::integer AS open_defacation
                    FROM building_info.buildings b 
                    LEFT JOIN building_info.structure_types st ON b.structure_type_id = st.id
                    LEFT JOIN building_info.sanitation_systems ss ON b.sanitation_system_id = ss.id
                    WHERE (ST_Intersects(ST_Buffer(ST_SetSRID(ST_Point(_param_long, _param_lat),4326)::GEOGRAPHY, _param_distance)::GEOMETRY, b.geom))
                    AND b.deleted_at is null
                    AND ss.map_display IS TRUE
                    GROUP BY b.structure_type_id, st.id 
                    ORDER BY st.id ASC
                ;
                END
                
                $$;",

        // "call_getPointBufferBuildings" => "Select * from fnc_getPointBufferBuildings($long, $lat, $distance);"
    ],
    

    // function to get buffer polygon buildings
    "fnc_getbufferpolygonbuildings" => [

        "fnc_drop_getbufferpolygonbuildings" => "DROP FUNCTION IF EXISTS fnc_getbufferpolygonbuildings(geometry,integer);",

        "fnc_set_getbufferpolygonbuildings" => "
        CREATE OR REPLACE FUNCTION fnc_getBufferPolygonBuildings(_param_bufferPolygonGeom geometry, _param_bufferDisancePolygon integer)
        RETURNS table (structype varchar(254), count integer,  sewer_network integer, drain_network integer, septic_tank integer, pit_holding_tank integer,
        onsite_treatment integer, composting_toilet integer, water_body integer, open_ground integer, community_toilet integer,
        open_defacation integer)  
        LANGUAGE plpgsql AS
        $$
        
        BEGIN
            RETURN Query
            SELECT st.type, COUNT(*)::integer AS count,
            COUNT(b.bin) filter (where b.sanitation_system_id = '1')::integer  AS sewer_network,
            COUNT(b.bin) filter (where b.sanitation_system_id = '2')::integer  AS drain_network,
            COUNT(b.bin) filter (where b.sanitation_system_id = '3')::integer AS septic_tank,
            COUNT(b.bin) filter (where b.sanitation_system_id = '4')::integer AS pit_holding_tank,
            COUNT(b.bin) filter (where b.sanitation_system_id = '5')::integer AS onsite_treatment,
            COUNT(b.bin) filter (where b.sanitation_system_id = '6')::integer AS composting_toilet,
            COUNT(b.bin) filter (where b.sanitation_system_id = '7')::integer AS water_body,
            COUNT(b.bin) filter (where b.sanitation_system_id = '8')::integer AS open_ground,
            COUNT(b.bin) filter (where b.sanitation_system_id = '9')::integer AS community_toilet,
            COUNT(b.bin) filter (where b.sanitation_system_id = '10')::integer AS open_defacation
        FROM building_info.buildings b 
                    LEFT JOIN building_info.structure_types st ON b.structure_type_id = st.id
                    LEFT JOIN building_info.sanitation_systems ss ON b.sanitation_system_id = ss.id

            WHERE (ST_Intersects(ST_Buffer(_param_bufferPolygonGeom::GEOGRAPHY, _param_bufferDisancePolygon)::GEOMETRY, b.geom))
            AND b.deleted_at is null 
            AND ss.map_display IS TRUE
            GROUP BY b.structure_type_id, st.id 
            ORDER BY st.id ASC
        ;
        END
        $$;",

        // "call_getbufferpolygonbuildings" => "Select * from fnc_getBufferPolygonBuildings( ST_GeomFromText("."'"."$bufferPolygonGeom"."'".",4326), $bufferDisancePolygon) ;",
    ],
    "get_ctpt_dependent_buildings_wreturngeom_linestring" => [
        "fnc_drop_ctpt_dependent_buildings_wreturngeom_linestring" => "DROP FUNCTION IF EXISTS public.get_ctpt_dependent_buildings_wreturngeom_linestring(character varying);",
        "fnc_get_ctpt_dependent_buildings_wreturngeom_linestring" => "CREATE OR REPLACE FUNCTION public.get_ctpt_dependent_buildings_wreturngeom_linestring(_building_id_param character varying)
                    RETURNS TABLE(linkage_geom geometry) 
                    LANGUAGE 'plpgsql'
                    AS $$
                    DECLARE   _toilet_id INTEGER;
                    BEGIN
                        SELECT id 
                            INTO _toilet_id 
                        FROM fsm.toilets 
                        WHERE bin = _building_id_param;
                        IF _toilet_id IS NOT NULL THEN
                            RETURN QUERY
                            SELECT 
                            ST_MakeLine(a.geom, ST_Centroid(b.geom))
                            FROM fsm.toilets a
                            LEFT JOIN LATERAL (
                                SELECT b.geom
                                FROM fsm.build_toilets bt 
                                JOIN building_info.buildings b ON bt.bin=b.bin AND b.deleted_at IS NULL
                                WHERE bt.toilet_id = _toilet_id 
                                AND bt.deleted_at IS NULL
                            ) b ON true
                            WHERE a.bin = _building_id_param
                            AND a.deleted_at IS NULL;
                        END IF;
                    END
                    $$;"
    ]

];