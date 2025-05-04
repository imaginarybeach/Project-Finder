./filecopy.sh
docker exec -i project_finder_db mysql -u root -p'rooty' < sql/1Create.sql
docker exec -i project_finder_db mysql -u root -p'rooty' < sql/2Load.sql