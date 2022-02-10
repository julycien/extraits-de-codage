 <?php

require_once '../entities/Carousel.php';

class CarouselDAO {

    private $pdo;

    function __construct(PDO $pdo) {
        $this->pdo = $pdo;
    }

    public function selectAll(): array {
        $tCarousel = array();
        try {
            $cursor = $this->pdo->query("SELECT * FROM carousel ORDER BY ordrephotocarousel");
            $cursor->setFetchMode(PDO::FETCH_ASSOC);
            while ($record = $cursor->fetch()) {
                $carousel = new Carousel($record["idphotocarousel"], $record["nomphotocarousel"], $record["ordrephotocarousel"]);
                $tCarousel[] = $carousel;
            }
        } catch (Exception $ex) {
            $tCarousel[] = new Carousel("-1", $ex->getMessage());
        }
        return $tCarousel;
    }
   
    public function selectOne($pk): Carousel {
        try {
            $cursor = $this->pdo->prepare("SELECT * FROM carousel WHERE idphotocarousel = ?");
            $cursor->bindParam(1, $pk);
            $cursor->execute();
            $record = $cursor->fetch();
            if ($record != null) {
                $carousel = new Carousel($record["idphotocarousel"], $record["nomphotocarousel"], $record["ordrephotocarousel"]);
            } else {
                $carousel = new Carousel("non trouvé");
            }
            //$cursor->close();
        } catch (Exception $exc) {
            $carousel = new Carousel("-1", $exc->getMessage());
            echo $exc;
        }
        return $carousel;
    }

    public function insert(Carousel $carousel): int {
        $affected = 0;
        try {
            $cmd = $this->pdo->prepare("INSERT INTO carousel(idphotocarousel, nomphotocarousel, ordrephotocarousel) "
                    . "VALUES(?,?,?)");
            $cmd->bindValue(1, $carousel->getIdPhotoCarousel());
            $cmd->bindValue(2, $carousel->getNomPhotoCarousel());
            $cmd->bindValue(3, $carousel->getOrdrePhotoCarousel());
           
            $cmd->execute();
            $affected = $cmd->rowCount();
        } catch (Exception $ex) {
            $affected = -1;
            echo $ex->getMessage();
        }
        return $affected;
    }

    public function delete(Carousel $carousel): int {
        $affected = 0;
        try {
            $lcmd = $this->pdo->prepare("DELETE FROM carousel WHERE idphotocarousel = ?");
            $lcmd->bindValue(1, $carousel->getIdPhotoCarousel());
            $lcmd->execute();
            $affected = $lcmd->rowCount();
        } catch (Exception $exc) {
            $affected = -1;
            echo $ex->getMessage();
        }
        return $affected;
    }
    
    public function update(Carousel $carousel): int {
        $affected = 0;
        try {
            $lcmd = $this->pdo->prepare("UPDATE carousel SET ordrephotocarousel = ?, nomphotocarousel = ? WHERE idphotocarousel = ?");
            $lcmd->bindValue(1, $carousel->getOrdrePhotoCarousel());
            $lcmd->bindValue(2, $carousel->getNomPhotoCarousel());
            $lcmd->bindValue(3, $carousel->getIdPhotoCarousel());
            $lcmd->execute();
            $affected = $lcmd->rowCount();
        } catch (Exception $exc) {
            $affected = -1;
            echo $ex->getMessage();
        }
        return $affected;
    }
    

}
