import React from "react";
import "../PlayerCard.css";
import defaultPlayerImage from "../img/default_user.png";

const PlayerCard = ({
  rating,
  position,
  player_image_url,
  playerName,
  pace,
  shooting,
  physical,
  defending,
  dribbling,
  passing,
}) => {
  const image = player_image_url ? player_image_url : defaultPlayerImage;
  const posicion = position ? position.split(",") : [""];
  return (
    <div className="wrapper">
      <div className="fut-player-card">
        <div className="player-card-top">
          <div className="player-master-info">
            <div className="player-rating">
              <span>{rating}</span>
            </div>
            <div className="player-position">
              <span>{posicion[0]}</span>
            </div>
          </div>

          <div className="player-picture">
            <img src={image} alt="Player" draggable="false" />
          </div>
        </div>
        <div className="player-card-bottom">
          <div className="player-info">
            <div className="player-name">
              <span>{playerName}</span>
            </div>
            <div className="player-features">
              <div className="player-features-col">
                <span>
                  <span className="player-feature-value">{pace}</span>
                  <span className="player-feature-title">PAC</span>
                </span>
                <span>
                  <span className="player-feature-value">{shooting}</span>
                  <span className="player-feature-title">SHO</span>
                </span>
                <span>
                  <span className="player-feature-value">{passing}</span>
                  <span className="player-feature-title">PAS</span>
                </span>
              </div>
              <div className="player-features-col">
                <span>
                  <span className="player-feature-value">{dribbling}</span>
                  <span className="player-feature-title">DRI</span>
                </span>
                <span>
                  <span className="player-feature-value">{defending}</span>
                  <span className="player-feature-title">DEF</span>
                </span>
                <span>
                  <span className="player-feature-value">{physical}</span>
                  <span className="player-feature-title">PHY</span>
                </span>
              </div>
            </div>
          </div>
        </div>
      </div>
    </div>
  );
};

export default PlayerCard;
