import React, { useState, useEffect } from "react";
import { useParams } from "react-router-dom";
import QuestionListCard from "../components/QuestionListCard";
import { loadQuestion } from "../actions/questionActions";

const QuestionDetailPage = () => {
  const { id } = useParams();
  const [loading, setLoading] = useState(true);
  const [loadFailed, setLoadFailed] = useState(false);
  const [question, setQuestion] = useState(null);
  useEffect(() => {
    setLoading(true);
    setLoadFailed(false);
    loadQuestion(id)
      .then(
        (response) => setQuestion(response.data),
        () => setLoadFailed(true)
      )
      .finally(() => setLoading(false));
  }, [id]);
  return (
    <>
      {loading && <p>Loading question&hellip;</p>}
      {loadFailed && <p>Failed to load question</p>}
      {!loading &&
        !loadFailed &&
        // TODO: Render an actual detail page here.
        question && <QuestionListCard question={question} />}
    </>
  );
};

export default QuestionDetailPage;
