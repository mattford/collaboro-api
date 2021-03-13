import React, { useState, useEffect } from "react";
import { loadQuestions } from "../actions/questionActions";
import QuestionListCard from "../components/QuestionListCard";

const QuestionsPage = () => {
  const [questions, setQuestions] = useState([]);
  const [loading, setLoading] = useState(true);
  const [loadFailed, setLoadFailed] = useState(false);
  useEffect(() => {
    loadQuestions()
      .then(
        (response) => setQuestions(response.data),
        () => setLoadFailed(true)
      )
      .finally(() => setLoading(false));
  }, []);
  return (
    <>
      {loading && <p>Loading questions&hellip;</p>}
      {loadFailed && <p>Failed to load questions</p>}
      {!loading &&
        !loadFailed &&
        questions &&
        questions.map((question) => (
          <QuestionListCard key={question.id} question={question} />
        ))}
    </>
  );
};

export default QuestionsPage;
