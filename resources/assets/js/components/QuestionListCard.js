import React from "react";
import { Card, Button } from "react-bootstrap";
import { useHistory } from "react-router-dom";
import moment from "moment";
import { FontAwesomeIcon } from "@fortawesome/react-fontawesome";
import { faChevronUp, faChevronDown } from "@fortawesome/free-solid-svg-icons";
import IconButton from "./InvisibleButton";

const QuestionListCard = ({ question, onVoteUp, onVoteDown }) => {
  const history = useHistory();
  const handleQuestionTitleClick = () => {
    history.push(`/question/${question.id}`);
  };
  return (
    <Card style={{ marginTop: "10px" }}>
      <Card.Body>
        <div
          className="votes"
          style={{
            display: "inline-flex",
            flexDirection: "column",
            alignItems: "center",
            verticalAlign: "top",
            marginRight: "20px",
          }}
        >
          <IconButton
            icon={<FontAwesomeIcon icon={faChevronUp} />}
            onClick={onVoteUp}
          />
          {question.votes ?? 0}
          <IconButton
            icon={<FontAwesomeIcon icon={faChevronDown} />}
            onClick={onVoteDown}
          />
        </div>
        <div className="info" style={{ display: "inline-block" }}>
          <Button variant="link p-0" onClick={handleQuestionTitleClick}>
            {question.title}
          </Button>
          <div>{question.content}</div>
          <div>
            Asked {moment(question.created_at).fromNow()} by{" "}
            {question.creator.name}
          </div>
        </div>
      </Card.Body>
    </Card>
  );
};

export default QuestionListCard;
