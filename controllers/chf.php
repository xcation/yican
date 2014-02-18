<?php
	/**
	 * 没有用到
	 */
	class Chf extends CI_Controller {
		public function __construct() {
			parent::__construct();
			$this->load->helper('cookie');
			$this->load->model('user_info');
			$this->load->library('session');
			$this->load->library('header');
			$this->load->model('about_us_info');
		}

		private function _get_login($userId, $passwd) {
			$q = $this->db->select('userId')
						  ->where('userId', $userId)
						  ->where('passwd', $passwd)
						  ->get('chfUserInfo');
			return $q;
		}

		private function _is_login() {
			if ($this->session->userdata("userId"))
				return true;
			return false;
		}

		private function _set_login($userId) {
			$this->session->set_userdata(array("userId"=>$userId));
		}

		public function web_login() {
			$userId = $this->input->post('userid');
			$passwd = $this->input->post('passwd');
			if ($userId && $passwd) {
				$q = $this->_get_login($userId, $passwd);
				if ($q->num_rows() > 0) {
					$this->_set_login($userId);
					header('location: /chf/select');
				}
			} else {
				$this->load->view('chf/login');
			}
		}

		public function web_register() {
			$userId = $this->input->post('userid');
			$passwd = $this->input->post('passwd');
			$passwd_again = $this->input->post('passwd_again');
			// var_dump($userId);
			// var_dump($passwd);
			// var_dump($passwd_again);
			$wrong = true;
			if ($userId && $passwd && $passwd_again) {
				if ($passwd == $passwd_again) {
					$q = $this->db->select('userId')
								  ->where('userId', $userId)
								  ->get('chfUserInfo');
					if ($q->num_rows() == 0) {
						$data = array('userId'=>$userId,
						              'passwd'=>$passwd);
						$this->db->insert('chfUserInfo', $data);
						$this->_set_login($userId);
						header('location: /chf/select');
					}
				}
			}
			if ($wrong)
				$this->load->view('chf/register');
		}

		public function select() {
			$this->load->view('chf/select');
		}
		public function web_create_check_in() {
			if (!$this->_is_login())
				return;
			$second = $this->input->post('expired_time');
			$random_code = $this->input->post('random_code');

			if ($second) {
				$userId = $this->session->userdata("userId");
				$expired_time = date('Y-m-d H:i:s');
				$tm = strtotime($expired_time);
				$tm += $second;
				$data = array('createUserId'=>$userId,
				              'endTime'=>date('Y-m-d H:i:s', $tm),
				              'inviteCode'=>$random_code);
				$this->db->insert('chfCheckIn', $data);

				$data = array('checkInId'=>$this->db->insert_id(),
				              'invitedId'=>$userId,
				              'isCheckIn'=>0);
				$this->db->insert('chfCheckInInvited', $data);
			}
			$this->load->view('chf/create_check_in');
		}
		public function web_create_quiz() {
			if ($this->_is_login()) {
				$description = $this->input->post('description');
				$title = $this->input->post('title');
				$question_text = $this->input->post('question_text');
				$answer_text = $this->input->post('answer_text');
				$correct = $this->input->post('correct');
				$time = $this->input->post('time');
				if ($description && $title && $question_text
				    && $answer_text && $correct) {

					// echo "description: ";
					// var_dump($description);
					// echo "title: ";
					// var_dump($title);
					// echo "question_text: ";
					// var_dump($question_text);
					// echo "answer_text: ";
					// var_dump($answer_text);
					// echo "correct: ";
					// var_dump($correct);
					$userId = $this->session->userdata("userId");

					$data = array('quizDecription'=>$description,
					              'quizTitle'=>$title,
					              'quizCreater'=>$userId,
					              'quizLastTime'=>$time);
					$this->db->insert('chfQuizInfo', $data);
					$quizId = $this->db->insert_id();

					$answer_text_counter = -1;
					foreach ($question_text as $one_question) {
						$data = array('questionText'=>$one_question,
						              'answerText_1'=>@$answer_text[++$answer_text_counter],
						              'correct_1'=>@$correct[$answer_text_counter],
						              'answerText_2'=>@$answer_text[++$answer_text_counter],
						              'correct_2'=>@$correct[$answer_text_counter],
						              'answerText_3'=>@$answer_text[++$answer_text_counter],
						              'correct_3'=>@$correct[$answer_text_counter],
						              'answerText_4'=>@$answer_text[++$answer_text_counter],
						              'correct_4'=>@$correct[$answer_text_counter],
						              'answerText_5'=>@$answer_text[++$answer_text_counter],
						              'correct_5'=>@$correct[$answer_text_counter],
						              );
						$this->db->insert('chfQuestionDetails', $data);
						$questionId = $this->db->insert_id();
						$data = array('questionId'=>$questionId,
						              'quizId'=>$quizId);
						$this->db->insert('chfQuizQuestion', $data);
					}
					$data = array('quizId'=>$quizId,
					              'invitedId'=>$userId);
					$this->db->insert('chfQuizInvited', $data);
				}
				$this->load->view('chf/create_quiz');
			} else {
				header('location: /chf/web_login');
			}
		}
		public function login() {
			$userId = urldecode($_GET['userid']);
			$passwd = urldecode($_GET['passwd']);
			if ($userid && $passwd) {
				$q = $this->_get_login($userId, $passwd);
				if ($q->num_rows() > 0) {
					$session = md5($userId.$passwd.date("Y-m-d H:i:s"));
					$this->db->set('sessionId', $session);
					$this->db->where('userId', $userId);
					$this->db->update('chfUserInfo');
					echo $session;
				}
			}
		}

		private function _getallheaders() {
			foreach ($_SERVER as $name => $value) {
			   if (substr($name, 0, 5) == 'HTTP_') {
			       $headers[str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', substr($name, 5)))))] = $value;
			   }
			}
			return $headers;
		}
		public function documents($userId) {
			$q = $this->db->select('chfQuizInvited.quizId as quizId, chfQuizInfo.quizTitle as quizTitle')
						  ->from('chfQuizInvited')
						  ->join('chfQuizInfo', 'chfQuizInfo.quizId = chfQuizInvited.quizId')
						  ->where('chfQuizInvited.invitedId', $userId)
						  ->where('chfQuizInvited.isSubmited', '0')
						  ->get();
			$re = array();
			foreach ($q->result_array() as $row) {
				$re[] = array('type' => 1,
				              'id'=>$row['quizId'],
				              'title'=>$row['quizTitle']);
			}
			header("content-type: application/json; charset=utf-8");
			echo json_encode($re);
		}

		public function quiz($quizId) {
			$userId = $this->_getallheaders();
			// $userId = "archychu";
			$userId = @$userId['Cookie'];
			// if (!$userId)
				// return;
			$q = $this->db->select('chfQuizInfo.quizTitle as quiz_title,
			                        chfQuizInfo.quizDecription as quiz_description,
			                        chfQuizInfo.quizLastTime as quiz_last_time,
			                        chfQuestionDetails.questionText as ques_text,
			                        chfQuestionDetails.answerText_1 as ques_ans_1,
			                        chfQuestionDetails.answerText_1 as ques_ans_1,
			                        chfQuestionDetails.answerText_2 as ques_ans_2,
			                        chfQuestionDetails.answerText_3 as ques_ans_3,
			                        chfQuestionDetails.answerText_4 as ques_ans_4,
			                        chfQuestionDetails.answerText_5 as ques_ans_5,
			                        chfQuestionDetails.correct_1 as ques_corr_1,
			                        chfQuestionDetails.correct_2 as ques_corr_2,
			                        chfQuestionDetails.correct_3 as ques_corr_3,
			                        chfQuestionDetails.correct_4 as ques_corr_4,
			                        chfQuestionDetails.correct_5 as ques_corr_5'
			                        )
						  ->from('chfQuizInfo')
						  ->join('chfQuizQuestion', 'chfQuizQuestion.quizId = chfQuizInfo.quizId')
						  ->join('chfQuizInvited', 'chfQuizInvited.quizId = chfQuizInfo.quizId')
						  ->join('chfQuestionDetails', 'chfQuestionDetails.questionId = chfQuizQuestion.questionId')
						  ->where('chfQuizInvited.quizId', $quizId)
						  ->get();
			$all_ques = array();
			foreach ($q->result_array() as $one_question) {
				$ans = array();
				for ($i = 1; $i <= 5; $i++) {
					if ($one_question['ques_ans_'.$i])
						$ans[] = array('answer_text'=>$one_question['ques_ans_'.$i],
						               'correct'=>($one_question['ques_corr_'.$i] == '1'),
						               'number'=>$i-1);
					else
						break;
				}

				$ques = array('question_text'=>$one_question['ques_text'],
				              'number'=>'1',
				              'answers'=>$ans);
				$all_ques[] = $ques;
			}

			$data = array();
			foreach ($q->result_array() as $one_question) {
				$data = array('description'=>$one_question['quiz_description'],
				              'title'=>$one_question['quiz_title'],
				              'image'=>'',
				              'leaderboard_sheet'=>'',
				              'questions'=>$all_ques,
				              'document_id'=>$quizId,
				              'statistics_sheet'=>'',
				              'last_time'=>$one_question['quiz_last_time']);
				break;
			}
			echo json_encode($data);
		}

		public function quiz_submit($quizId, $userId) {
			$json = $GLOBALS['HTTP_RAW_POST_DATA'];
			$json = $json;
			$this->db->set('isSubmited', '1');
			$this->db->where('quizId', $quizId);
			$this->db->where('invitedId', $userId);
			$this->db->update('chfQuizInvited');
			echo "0";
		}

		public function all_check_in($userId) {
			$q = $this->db->select('chfCheckIn.createUserId as invite_id,
			                        chfCheckIn.checkInId as check_in_id')
						  ->from('chfCheckInInvited')
						  ->join('chfCheckIn', 'chfCheckInInvited.checkInId = chfCheckIn.checkInId')
						  ->where('chfCheckInInvited.invitedId', $userId)
						  ->where('chfCheckInInvited.isCheckIn', '0')
						  ->where('unix_timestamp(chfCheckIn.endTime) > unix_timestamp(now())')
						  ->get();
			$data = array();
			foreach ($q->result_array() as $row) {
				$data[] = array('inviter'=>$row['invite_id'],
				                'check_in_id'=>$row['check_in_id']);
			}
			echo json_encode($data);
		}

		public function get_check_code($check_id) {
			$q = $this->db->select('inviteCode as invite_code,
			                        checkInId as check_in_id,
			                        endTime as expired_time,
			                        createUserId as inviter')
						  ->where('chfCheckIn.checkInId', $check_id)
						  ->get('chfCheckIn');;
			foreach ($q->result_array() as $row) {
				$data = array('inviter'=>$row['inviter'],
				                'check_id'=>$row['check_in_id'],
				                'check_code'=>$row['invite_code'],
				                'expired_time'=>$row['expired_time']);
				echo json_encode($data);
				break;
			}
		}

		public function check_code_submit($check_id) {
			$this->db->where('checkInId', $check_id);
			$this->db->set('isCheckIn', '1');
			$this->db->set('checkInTime', date("Y-m-d H:i:s"));
			$this->db->update('chfCheckInInvited');
			echo "1";
		}
	}

