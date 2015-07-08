/* Include files */

#include "blascompat32.h"
#include "heart_model_sfun.h"
#include "c38_heart_model.h"
#define CHARTINSTANCE_CHARTNUMBER      (chartInstance->chartNumber)
#define CHARTINSTANCE_INSTANCENUMBER   (chartInstance->instanceNumber)
#include "heart_model_sfun_debug_macros.h"

/* Type Definitions */

/* Named Constants */
#define c38_event_event1               (0)
#define c38_event_event2               (2)
#define c38_event_event3               (3)
#define c38_event_pstim                (4)
#define c38_event_endpstim             (5)
#define CALL_EVENT                     (-1)
#define c38_IN_NO_ACTIVE_CHILD         ((uint8_T)0U)
#define c38_IN_resting                 ((uint8_T)2U)
#define c38_IN_stimulated              ((uint8_T)3U)
#define c38_IN_plateau                 ((uint8_T)1U)
#define c38_IN_upstroke                ((uint8_T)4U)

/* Variable Declarations */

/* Variable Definitions */

/* Function Declarations */
static void initialize_c38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance);
static void initialize_params_c38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance);
static void enable_c38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance);
static void disable_c38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance);
static void c38_update_debugger_state_c38_heart_model
  (SFc38_heart_modelInstanceStruct *chartInstance);
static const mxArray *get_sim_state_c38_heart_model
  (SFc38_heart_modelInstanceStruct *chartInstance);
static void set_sim_state_c38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_st);
static void c38_set_sim_state_side_effects_c38_heart_model
  (SFc38_heart_modelInstanceStruct *chartInstance);
static void finalize_c38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance);
static void sf_c38_heart_model(SFc38_heart_modelInstanceStruct *chartInstance);
static void c38_chartstep_c38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance);
static void initSimStructsc38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance);
static void init_script_number_translation(uint32_T c38_machineNumber, uint32_T
  c38_chartNumber);
static const mxArray *c38_sf_marshallOut(void *chartInstanceVoid, void
  *c38_inData);
static int8_T c38_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_u, const emlrtMsgIdentifier *c38_parentId);
static void c38_sf_marshallIn(void *chartInstanceVoid, const mxArray
  *c38_mxArrayInData, const char_T *c38_varName, void *c38_outData);
static const mxArray *c38_b_sf_marshallOut(void *chartInstanceVoid, void
  *c38_inData);
static uint32_T c38_b_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_b_resetEventCounter, const char_T
  *c38_identifier);
static uint32_T c38_c_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_u, const emlrtMsgIdentifier *c38_parentId);
static void c38_b_sf_marshallIn(void *chartInstanceVoid, const mxArray
  *c38_mxArrayInData, const char_T *c38_varName, void *c38_outData);
static const mxArray *c38_c_sf_marshallOut(void *chartInstanceVoid, void
  *c38_inData);
static int32_T c38_d_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_u, const emlrtMsgIdentifier *c38_parentId);
static void c38_c_sf_marshallIn(void *chartInstanceVoid, const mxArray
  *c38_mxArrayInData, const char_T *c38_varName, void *c38_outData);
static const mxArray *c38_d_sf_marshallOut(void *chartInstanceVoid, void
  *c38_inData);
static uint8_T c38_e_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_b_tp_resting, const char_T *c38_identifier);
static uint8_T c38_f_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_u, const emlrtMsgIdentifier *c38_parentId);
static void c38_d_sf_marshallIn(void *chartInstanceVoid, const mxArray
  *c38_mxArrayInData, const char_T *c38_varName, void *c38_outData);
static const mxArray *c38_e_sf_marshallOut(void *chartInstanceVoid, void
  *c38_inData);
static real_T c38_g_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_q, const char_T *c38_identifier);
static real_T c38_h_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_u, const emlrtMsgIdentifier *c38_parentId);
static void c38_e_sf_marshallIn(void *chartInstanceVoid, const mxArray
  *c38_mxArrayInData, const char_T *c38_varName, void *c38_outData);
static boolean_T c38_i_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_reset, const char_T *c38_identifier);
static boolean_T c38_j_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_u, const emlrtMsgIdentifier *c38_parentId);
static const mxArray *c38_k_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_b_setSimStateSideEffectsInfo, const char_T *
  c38_identifier);
static const mxArray *c38_l_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_u, const emlrtMsgIdentifier *c38_parentId);
static void init_dsm_address_info(SFc38_heart_modelInstanceStruct *chartInstance);

/* Function Definitions */
static void initialize_c38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance)
{
  real_T *c38_q;
  real_T *c38_vreset;
  real_T *c38_vno;
  boolean_T *c38_reset;
  c38_reset = (boolean_T *)ssGetOutputPortSignal(chartInstance->S, 4);
  c38_vno = (real_T *)ssGetOutputPortSignal(chartInstance->S, 3);
  c38_vreset = (real_T *)ssGetOutputPortSignal(chartInstance->S, 2);
  c38_q = (real_T *)ssGetOutputPortSignal(chartInstance->S, 1);
  _sfTime_ = (real_T)ssGetT(chartInstance->S);
  chartInstance->c38_doSetSimStateSideEffects = 0U;
  chartInstance->c38_setSimStateSideEffectsInfo = NULL;
  chartInstance->c38_tp_plateau = 0U;
  chartInstance->c38_tp_resting = 0U;
  chartInstance->c38_tp_stimulated = 0U;
  chartInstance->c38_tp_upstroke = 0U;
  chartInstance->c38_is_active_c38_heart_model = 0U;
  chartInstance->c38_is_c38_heart_model = 0U;
  chartInstance->c38_vn = 0.0;
  if (!(cdrGetOutputPortReusable(chartInstance->S, 1) != 0)) {
    *c38_q = 1.0;
  }

  if (!(cdrGetOutputPortReusable(chartInstance->S, 2) != 0)) {
    *c38_vreset = 0.0;
  }

  if (!(cdrGetOutputPortReusable(chartInstance->S, 3) != 0)) {
    *c38_vno = 0.0;
  }

  chartInstance->c38_resetEventCounter = 0U;
  *c38_reset = FALSE;
}

static void initialize_params_c38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance)
{
}

static void enable_c38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance)
{
  _sfTime_ = (real_T)ssGetT(chartInstance->S);
}

static void disable_c38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance)
{
  _sfTime_ = (real_T)ssGetT(chartInstance->S);
}

static void c38_update_debugger_state_c38_heart_model
  (SFc38_heart_modelInstanceStruct *chartInstance)
{
  uint32_T c38_prevAniVal;
  c38_prevAniVal = sf_debug_get_animation();
  sf_debug_set_animation(0U);
  if (chartInstance->c38_is_active_c38_heart_model == 1) {
    _SFD_CC_CALL(CHART_ACTIVE_TAG, 35U, chartInstance->c38_sfEvent);
  }

  if (chartInstance->c38_is_c38_heart_model == c38_IN_resting) {
    _SFD_CS_CALL(STATE_ACTIVE_TAG, 1U, chartInstance->c38_sfEvent);
  } else {
    _SFD_CS_CALL(STATE_INACTIVE_TAG, 1U, chartInstance->c38_sfEvent);
  }

  if (chartInstance->c38_is_c38_heart_model == c38_IN_stimulated) {
    _SFD_CS_CALL(STATE_ACTIVE_TAG, 2U, chartInstance->c38_sfEvent);
  } else {
    _SFD_CS_CALL(STATE_INACTIVE_TAG, 2U, chartInstance->c38_sfEvent);
  }

  if (chartInstance->c38_is_c38_heart_model == c38_IN_plateau) {
    _SFD_CS_CALL(STATE_ACTIVE_TAG, 0U, chartInstance->c38_sfEvent);
  } else {
    _SFD_CS_CALL(STATE_INACTIVE_TAG, 0U, chartInstance->c38_sfEvent);
  }

  if (chartInstance->c38_is_c38_heart_model == c38_IN_upstroke) {
    _SFD_CS_CALL(STATE_ACTIVE_TAG, 3U, chartInstance->c38_sfEvent);
  } else {
    _SFD_CS_CALL(STATE_INACTIVE_TAG, 3U, chartInstance->c38_sfEvent);
  }

  sf_debug_set_animation(c38_prevAniVal);
  _SFD_ANIMATE();
}

static const mxArray *get_sim_state_c38_heart_model
  (SFc38_heart_modelInstanceStruct *chartInstance)
{
  const mxArray *c38_st;
  const mxArray *c38_y = NULL;
  real_T c38_hoistedGlobal;
  real_T c38_u;
  const mxArray *c38_b_y = NULL;
  real_T c38_b_hoistedGlobal;
  real_T c38_b_u;
  const mxArray *c38_c_y = NULL;
  real_T c38_c_hoistedGlobal;
  real_T c38_c_u;
  const mxArray *c38_d_y = NULL;
  real_T c38_d_hoistedGlobal;
  real_T c38_d_u;
  const mxArray *c38_e_y = NULL;
  boolean_T c38_e_hoistedGlobal;
  boolean_T c38_e_u;
  const mxArray *c38_f_y = NULL;
  uint32_T c38_f_hoistedGlobal;
  uint32_T c38_f_u;
  const mxArray *c38_g_y = NULL;
  uint8_T c38_g_hoistedGlobal;
  uint8_T c38_g_u;
  const mxArray *c38_h_y = NULL;
  uint8_T c38_h_hoistedGlobal;
  uint8_T c38_h_u;
  const mxArray *c38_i_y = NULL;
  real_T *c38_q;
  real_T *c38_vno;
  real_T *c38_vreset;
  boolean_T *c38_reset;
  c38_reset = (boolean_T *)ssGetOutputPortSignal(chartInstance->S, 4);
  c38_vno = (real_T *)ssGetOutputPortSignal(chartInstance->S, 3);
  c38_vreset = (real_T *)ssGetOutputPortSignal(chartInstance->S, 2);
  c38_q = (real_T *)ssGetOutputPortSignal(chartInstance->S, 1);
  c38_st = NULL;
  c38_st = NULL;
  c38_y = NULL;
  sf_mex_assign(&c38_y, sf_mex_createcellarray(8), FALSE);
  c38_hoistedGlobal = *c38_q;
  c38_u = c38_hoistedGlobal;
  c38_b_y = NULL;
  sf_mex_assign(&c38_b_y, sf_mex_create("y", &c38_u, 0, 0U, 0U, 0U, 0), FALSE);
  sf_mex_setcell(c38_y, 0, c38_b_y);
  c38_b_hoistedGlobal = *c38_vno;
  c38_b_u = c38_b_hoistedGlobal;
  c38_c_y = NULL;
  sf_mex_assign(&c38_c_y, sf_mex_create("y", &c38_b_u, 0, 0U, 0U, 0U, 0), FALSE);
  sf_mex_setcell(c38_y, 1, c38_c_y);
  c38_c_hoistedGlobal = *c38_vreset;
  c38_c_u = c38_c_hoistedGlobal;
  c38_d_y = NULL;
  sf_mex_assign(&c38_d_y, sf_mex_create("y", &c38_c_u, 0, 0U, 0U, 0U, 0), FALSE);
  sf_mex_setcell(c38_y, 2, c38_d_y);
  c38_d_hoistedGlobal = chartInstance->c38_vn;
  c38_d_u = c38_d_hoistedGlobal;
  c38_e_y = NULL;
  sf_mex_assign(&c38_e_y, sf_mex_create("y", &c38_d_u, 0, 0U, 0U, 0U, 0), FALSE);
  sf_mex_setcell(c38_y, 3, c38_e_y);
  c38_e_hoistedGlobal = *c38_reset;
  c38_e_u = c38_e_hoistedGlobal;
  c38_f_y = NULL;
  sf_mex_assign(&c38_f_y, sf_mex_create("y", &c38_e_u, 11, 0U, 0U, 0U, 0), FALSE);
  sf_mex_setcell(c38_y, 4, c38_f_y);
  c38_f_hoistedGlobal = chartInstance->c38_resetEventCounter;
  c38_f_u = c38_f_hoistedGlobal;
  c38_g_y = NULL;
  sf_mex_assign(&c38_g_y, sf_mex_create("y", &c38_f_u, 7, 0U, 0U, 0U, 0), FALSE);
  sf_mex_setcell(c38_y, 5, c38_g_y);
  c38_g_hoistedGlobal = chartInstance->c38_is_active_c38_heart_model;
  c38_g_u = c38_g_hoistedGlobal;
  c38_h_y = NULL;
  sf_mex_assign(&c38_h_y, sf_mex_create("y", &c38_g_u, 3, 0U, 0U, 0U, 0), FALSE);
  sf_mex_setcell(c38_y, 6, c38_h_y);
  c38_h_hoistedGlobal = chartInstance->c38_is_c38_heart_model;
  c38_h_u = c38_h_hoistedGlobal;
  c38_i_y = NULL;
  sf_mex_assign(&c38_i_y, sf_mex_create("y", &c38_h_u, 3, 0U, 0U, 0U, 0), FALSE);
  sf_mex_setcell(c38_y, 7, c38_i_y);
  sf_mex_assign(&c38_st, c38_y, FALSE);
  return c38_st;
}

static void set_sim_state_c38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_st)
{
  const mxArray *c38_u;
  real_T *c38_q;
  real_T *c38_vno;
  real_T *c38_vreset;
  boolean_T *c38_reset;
  c38_reset = (boolean_T *)ssGetOutputPortSignal(chartInstance->S, 4);
  c38_vno = (real_T *)ssGetOutputPortSignal(chartInstance->S, 3);
  c38_vreset = (real_T *)ssGetOutputPortSignal(chartInstance->S, 2);
  c38_q = (real_T *)ssGetOutputPortSignal(chartInstance->S, 1);
  c38_u = sf_mex_dup(c38_st);
  *c38_q = c38_g_emlrt_marshallIn(chartInstance, sf_mex_dup(sf_mex_getcell(c38_u,
    0)), "q");
  *c38_vno = c38_g_emlrt_marshallIn(chartInstance, sf_mex_dup(sf_mex_getcell
    (c38_u, 1)), "vno");
  *c38_vreset = c38_g_emlrt_marshallIn(chartInstance, sf_mex_dup(sf_mex_getcell
    (c38_u, 2)), "vreset");
  chartInstance->c38_vn = c38_g_emlrt_marshallIn(chartInstance, sf_mex_dup
    (sf_mex_getcell(c38_u, 3)), "vn");
  *c38_reset = c38_i_emlrt_marshallIn(chartInstance, sf_mex_dup(sf_mex_getcell
    (c38_u, 4)), "reset");
  chartInstance->c38_resetEventCounter = c38_b_emlrt_marshallIn(chartInstance,
    sf_mex_dup(sf_mex_getcell(c38_u, 5)), "resetEventCounter");
  chartInstance->c38_is_active_c38_heart_model = c38_e_emlrt_marshallIn
    (chartInstance, sf_mex_dup(sf_mex_getcell(c38_u, 6)),
     "is_active_c38_heart_model");
  chartInstance->c38_is_c38_heart_model = c38_e_emlrt_marshallIn(chartInstance,
    sf_mex_dup(sf_mex_getcell(c38_u, 7)), "is_c38_heart_model");
  sf_mex_assign(&chartInstance->c38_setSimStateSideEffectsInfo,
                c38_k_emlrt_marshallIn(chartInstance, sf_mex_dup(sf_mex_getcell
    (c38_u, 8)), "setSimStateSideEffectsInfo"), TRUE);
  sf_mex_destroy(&c38_u);
  chartInstance->c38_doSetSimStateSideEffects = 1U;
  c38_update_debugger_state_c38_heart_model(chartInstance);
  sf_mex_destroy(&c38_st);
}

static void c38_set_sim_state_side_effects_c38_heart_model
  (SFc38_heart_modelInstanceStruct *chartInstance)
{
  if (chartInstance->c38_doSetSimStateSideEffects != 0) {
    if (chartInstance->c38_is_c38_heart_model == c38_IN_plateau) {
      chartInstance->c38_tp_plateau = 1U;
    } else {
      chartInstance->c38_tp_plateau = 0U;
    }

    if (chartInstance->c38_is_c38_heart_model == c38_IN_resting) {
      chartInstance->c38_tp_resting = 1U;
    } else {
      chartInstance->c38_tp_resting = 0U;
    }

    if (chartInstance->c38_is_c38_heart_model == c38_IN_stimulated) {
      chartInstance->c38_tp_stimulated = 1U;
    } else {
      chartInstance->c38_tp_stimulated = 0U;
    }

    if (chartInstance->c38_is_c38_heart_model == c38_IN_upstroke) {
      chartInstance->c38_tp_upstroke = 1U;
    } else {
      chartInstance->c38_tp_upstroke = 0U;
    }

    chartInstance->c38_doSetSimStateSideEffects = 0U;
  }
}

static void finalize_c38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance)
{
  sf_mex_destroy(&chartInstance->c38_setSimStateSideEffectsInfo);
}

static void sf_c38_heart_model(SFc38_heart_modelInstanceStruct *chartInstance)
{
  int32_T c38_inputEventFiredFlag;
  real_T *c38_q;
  real_T *c38_vreset;
  real_T *c38_v;
  real_T *c38_vno;
  int8_T *c38_event1;
  int8_T *c38_event2;
  int8_T *c38_event3;
  int8_T *c38_pstim;
  int8_T *c38_endpstim;
  boolean_T *c38_reset;
  c38_reset = (boolean_T *)ssGetOutputPortSignal(chartInstance->S, 4);
  c38_endpstim = (int8_T *)*(ssGetInputPortSignalPtrs(chartInstance->S, 1) + 4);
  c38_pstim = (int8_T *)*(ssGetInputPortSignalPtrs(chartInstance->S, 1) + 3);
  c38_event3 = (int8_T *)*(ssGetInputPortSignalPtrs(chartInstance->S, 1) + 2);
  c38_event2 = (int8_T *)*(ssGetInputPortSignalPtrs(chartInstance->S, 1) + 1);
  c38_event1 = (int8_T *)*(ssGetInputPortSignalPtrs(chartInstance->S, 1) + 0);
  c38_vno = (real_T *)ssGetOutputPortSignal(chartInstance->S, 3);
  c38_v = (real_T *)ssGetInputPortSignal(chartInstance->S, 0);
  c38_vreset = (real_T *)ssGetOutputPortSignal(chartInstance->S, 2);
  c38_q = (real_T *)ssGetOutputPortSignal(chartInstance->S, 1);
  c38_set_sim_state_side_effects_c38_heart_model(chartInstance);
  _sfTime_ = (real_T)ssGetT(chartInstance->S);
  _SFD_CC_CALL(CHART_ENTER_SFUNCTION_TAG, 35U, chartInstance->c38_sfEvent);
  _SFD_DATA_RANGE_CHECK_MIN_MAX(*c38_q, 0U, 1.0, 4.0);
  _SFD_DATA_RANGE_CHECK(*c38_vreset, 1U);
  _SFD_DATA_RANGE_CHECK(*c38_v, 2U);
  _SFD_DATA_RANGE_CHECK(chartInstance->c38_vn, 3U);
  _SFD_DATA_RANGE_CHECK(*c38_vno, 4U);
  c38_inputEventFiredFlag = 0;
  if (*c38_event1 != 0) {
    c38_inputEventFiredFlag = 1;
    chartInstance->c38_sfEvent = c38_event_event1;
    _SFD_CE_CALL(EVENT_BEFORE_BROADCAST_TAG, c38_event_event1,
                 chartInstance->c38_sfEvent);
    c38_chartstep_c38_heart_model(chartInstance);
    _SFD_CE_CALL(EVENT_AFTER_BROADCAST_TAG, c38_event_event1,
                 chartInstance->c38_sfEvent);
  }

  if (*c38_event2 != 0) {
    c38_inputEventFiredFlag = 1;
    chartInstance->c38_sfEvent = c38_event_event2;
    _SFD_CE_CALL(EVENT_BEFORE_BROADCAST_TAG, c38_event_event2,
                 chartInstance->c38_sfEvent);
    c38_chartstep_c38_heart_model(chartInstance);
    _SFD_CE_CALL(EVENT_AFTER_BROADCAST_TAG, c38_event_event2,
                 chartInstance->c38_sfEvent);
  }

  if (*c38_event3 != 0) {
    c38_inputEventFiredFlag = 1;
    chartInstance->c38_sfEvent = c38_event_event3;
    _SFD_CE_CALL(EVENT_BEFORE_BROADCAST_TAG, c38_event_event3,
                 chartInstance->c38_sfEvent);
    c38_chartstep_c38_heart_model(chartInstance);
    _SFD_CE_CALL(EVENT_AFTER_BROADCAST_TAG, c38_event_event3,
                 chartInstance->c38_sfEvent);
  }

  if (*c38_pstim == 1) {
    c38_inputEventFiredFlag = 1;
    chartInstance->c38_sfEvent = c38_event_pstim;
    _SFD_CE_CALL(EVENT_BEFORE_BROADCAST_TAG, c38_event_pstim,
                 chartInstance->c38_sfEvent);
    c38_chartstep_c38_heart_model(chartInstance);
    _SFD_CE_CALL(EVENT_AFTER_BROADCAST_TAG, c38_event_pstim,
                 chartInstance->c38_sfEvent);
  }

  if (*c38_endpstim == -1) {
    c38_inputEventFiredFlag = 1;
    chartInstance->c38_sfEvent = c38_event_endpstim;
    _SFD_CE_CALL(EVENT_BEFORE_BROADCAST_TAG, c38_event_endpstim,
                 chartInstance->c38_sfEvent);
    c38_chartstep_c38_heart_model(chartInstance);
    _SFD_CE_CALL(EVENT_AFTER_BROADCAST_TAG, c38_event_endpstim,
                 chartInstance->c38_sfEvent);
  }

  if (c38_inputEventFiredFlag != 0) {
    if (chartInstance->c38_resetEventCounter > 0U) {
      *c38_reset = !*c38_reset;
      chartInstance->c38_resetEventCounter--;
    }
  }

  sf_debug_check_for_state_inconsistency(_heart_modelMachineNumber_,
    chartInstance->chartNumber, chartInstance->instanceNumber);
}

static void c38_chartstep_c38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance)
{
  boolean_T c38_out;
  boolean_T c38_b_out;
  boolean_T c38_c_out;
  boolean_T c38_d_out;
  boolean_T c38_e_out;
  boolean_T c38_f_out;
  boolean_T c38_g_out;
  boolean_T c38_h_out;
  real_T *c38_v;
  real_T *c38_vreset;
  real_T *c38_q;
  real_T *c38_vno;
  c38_vno = (real_T *)ssGetOutputPortSignal(chartInstance->S, 3);
  c38_v = (real_T *)ssGetInputPortSignal(chartInstance->S, 0);
  c38_vreset = (real_T *)ssGetOutputPortSignal(chartInstance->S, 2);
  c38_q = (real_T *)ssGetOutputPortSignal(chartInstance->S, 1);
  _SFD_CC_CALL(CHART_ENTER_DURING_FUNCTION_TAG, 35U, chartInstance->c38_sfEvent);
  if (chartInstance->c38_is_active_c38_heart_model == 0) {
    _SFD_CC_CALL(CHART_ENTER_ENTRY_FUNCTION_TAG, 35U, chartInstance->c38_sfEvent);
    chartInstance->c38_is_active_c38_heart_model = 1U;
    _SFD_CC_CALL(EXIT_OUT_OF_FUNCTION_TAG, 35U, chartInstance->c38_sfEvent);
    _SFD_CT_CALL(TRANSITION_BEFORE_PROCESSING_TAG, 3U,
                 chartInstance->c38_sfEvent);
    _SFD_CT_CALL(TRANSITION_ACTIVE_TAG, 3U, chartInstance->c38_sfEvent);
    chartInstance->c38_is_c38_heart_model = c38_IN_resting;
    _SFD_CS_CALL(STATE_ACTIVE_TAG, 1U, chartInstance->c38_sfEvent);
    chartInstance->c38_tp_resting = 1U;
    *c38_q = 1.0;
    _SFD_DATA_RANGE_CHECK_MIN_MAX(*c38_q, 0U, 1.0, 4.0);
  } else {
    switch (chartInstance->c38_is_c38_heart_model) {
     case c38_IN_plateau:
      CV_CHART_EVAL(35, 0, 1);
      _SFD_CS_CALL(STATE_ENTER_DURING_FUNCTION_TAG, 0U,
                   chartInstance->c38_sfEvent);
      _SFD_CT_CALL(TRANSITION_BEFORE_PROCESSING_TAG, 1U,
                   chartInstance->c38_sfEvent);
      c38_out = (CV_TRANSITION_EVAL(1U, (int32_T)_SFD_CCP_CALL(1U, 0,
        chartInstance->c38_sfEvent == c38_event_event1 != 0U,
        chartInstance->c38_sfEvent)) != 0);
      if (c38_out) {
        _SFD_CT_CALL(TRANSITION_ACTIVE_TAG, 1U, chartInstance->c38_sfEvent);
        chartInstance->c38_tp_plateau = 0U;
        chartInstance->c38_is_c38_heart_model = c38_IN_NO_ACTIVE_CHILD;
        _SFD_CS_CALL(STATE_INACTIVE_TAG, 0U, chartInstance->c38_sfEvent);
        *c38_vreset = *c38_v;
        _SFD_DATA_RANGE_CHECK(*c38_vreset, 1U);
        chartInstance->c38_resetEventCounter++;
        chartInstance->c38_is_c38_heart_model = c38_IN_resting;
        _SFD_CS_CALL(STATE_ACTIVE_TAG, 1U, chartInstance->c38_sfEvent);
        chartInstance->c38_tp_resting = 1U;
        *c38_q = 1.0;
        _SFD_DATA_RANGE_CHECK_MIN_MAX(*c38_q, 0U, 1.0, 4.0);
      }

      _SFD_CS_CALL(EXIT_OUT_OF_FUNCTION_TAG, 0U, chartInstance->c38_sfEvent);
      break;

     case c38_IN_resting:
      CV_CHART_EVAL(35, 0, 2);
      _SFD_CS_CALL(STATE_ENTER_DURING_FUNCTION_TAG, 1U,
                   chartInstance->c38_sfEvent);
      _SFD_CT_CALL(TRANSITION_BEFORE_PROCESSING_TAG, 4U,
                   chartInstance->c38_sfEvent);
      c38_b_out = (CV_TRANSITION_EVAL(4U, (int32_T)_SFD_CCP_CALL(4U, 0,
        chartInstance->c38_sfEvent == c38_event_pstim != 0U,
        chartInstance->c38_sfEvent)) != 0);
      if (c38_b_out) {
        if (sf_debug_transition_conflict_check_enabled()) {
          unsigned int transitionList[2];
          unsigned int numTransitions = 1;
          transitionList[0] = 4;
          sf_debug_transition_conflict_check_begin();
          c38_c_out = (chartInstance->c38_sfEvent == c38_event_event2);
          if (c38_c_out) {
            transitionList[numTransitions] = 6;
            numTransitions++;
          }

          sf_debug_transition_conflict_check_end();
          if (numTransitions > 1) {
            _SFD_TRANSITION_CONFLICT(&(transitionList[0]),numTransitions);
          }
        }

        _SFD_CT_CALL(TRANSITION_ACTIVE_TAG, 4U, chartInstance->c38_sfEvent);
        chartInstance->c38_tp_resting = 0U;
        chartInstance->c38_is_c38_heart_model = c38_IN_NO_ACTIVE_CHILD;
        _SFD_CS_CALL(STATE_INACTIVE_TAG, 1U, chartInstance->c38_sfEvent);
        *c38_vreset = *c38_v;
        _SFD_DATA_RANGE_CHECK(*c38_vreset, 1U);
        chartInstance->c38_vn = *c38_v;
        _SFD_DATA_RANGE_CHECK(chartInstance->c38_vn, 3U);
        chartInstance->c38_resetEventCounter++;
        chartInstance->c38_is_c38_heart_model = c38_IN_stimulated;
        _SFD_CS_CALL(STATE_ACTIVE_TAG, 2U, chartInstance->c38_sfEvent);
        chartInstance->c38_tp_stimulated = 1U;
        *c38_q = 2.0;
        _SFD_DATA_RANGE_CHECK_MIN_MAX(*c38_q, 0U, 1.0, 4.0);
      } else {
        _SFD_CT_CALL(TRANSITION_BEFORE_PROCESSING_TAG, 6U,
                     chartInstance->c38_sfEvent);
        c38_d_out = (CV_TRANSITION_EVAL(6U, (int32_T)_SFD_CCP_CALL(6U, 0,
          chartInstance->c38_sfEvent == c38_event_event2 != 0U,
          chartInstance->c38_sfEvent)) != 0);
        if (c38_d_out) {
          _SFD_CT_CALL(TRANSITION_ACTIVE_TAG, 6U, chartInstance->c38_sfEvent);
          chartInstance->c38_tp_resting = 0U;
          chartInstance->c38_is_c38_heart_model = c38_IN_NO_ACTIVE_CHILD;
          _SFD_CS_CALL(STATE_INACTIVE_TAG, 1U, chartInstance->c38_sfEvent);
          *c38_vreset = *c38_v;
          _SFD_DATA_RANGE_CHECK(*c38_vreset, 1U);
          chartInstance->c38_resetEventCounter++;
          chartInstance->c38_is_c38_heart_model = c38_IN_upstroke;
          _SFD_CS_CALL(STATE_ACTIVE_TAG, 3U, chartInstance->c38_sfEvent);
          chartInstance->c38_tp_upstroke = 1U;
          *c38_q = 3.0;
          _SFD_DATA_RANGE_CHECK_MIN_MAX(*c38_q, 0U, 1.0, 4.0);
        }
      }

      _SFD_CS_CALL(EXIT_OUT_OF_FUNCTION_TAG, 1U, chartInstance->c38_sfEvent);
      break;

     case c38_IN_stimulated:
      CV_CHART_EVAL(35, 0, 3);
      _SFD_CS_CALL(STATE_ENTER_DURING_FUNCTION_TAG, 2U,
                   chartInstance->c38_sfEvent);
      _SFD_CT_CALL(TRANSITION_BEFORE_PROCESSING_TAG, 0U,
                   chartInstance->c38_sfEvent);
      c38_e_out = (CV_TRANSITION_EVAL(0U, (int32_T)_SFD_CCP_CALL(0U, 0,
        chartInstance->c38_sfEvent == c38_event_event2 != 0U,
        chartInstance->c38_sfEvent)) != 0);
      if (c38_e_out) {
        if (sf_debug_transition_conflict_check_enabled()) {
          unsigned int transitionList[2];
          unsigned int numTransitions = 1;
          transitionList[0] = 0;
          sf_debug_transition_conflict_check_begin();
          c38_f_out = (chartInstance->c38_sfEvent == c38_event_endpstim);
          if (c38_f_out) {
            transitionList[numTransitions] = 5;
            numTransitions++;
          }

          sf_debug_transition_conflict_check_end();
          if (numTransitions > 1) {
            _SFD_TRANSITION_CONFLICT(&(transitionList[0]),numTransitions);
          }
        }

        _SFD_CT_CALL(TRANSITION_ACTIVE_TAG, 0U, chartInstance->c38_sfEvent);
        chartInstance->c38_tp_stimulated = 0U;
        chartInstance->c38_is_c38_heart_model = c38_IN_NO_ACTIVE_CHILD;
        _SFD_CS_CALL(STATE_INACTIVE_TAG, 2U, chartInstance->c38_sfEvent);
        *c38_vreset = *c38_v;
        _SFD_DATA_RANGE_CHECK(*c38_vreset, 1U);
        chartInstance->c38_resetEventCounter++;
        chartInstance->c38_is_c38_heart_model = c38_IN_upstroke;
        _SFD_CS_CALL(STATE_ACTIVE_TAG, 3U, chartInstance->c38_sfEvent);
        chartInstance->c38_tp_upstroke = 1U;
        *c38_q = 3.0;
        _SFD_DATA_RANGE_CHECK_MIN_MAX(*c38_q, 0U, 1.0, 4.0);
      } else {
        _SFD_CT_CALL(TRANSITION_BEFORE_PROCESSING_TAG, 5U,
                     chartInstance->c38_sfEvent);
        c38_g_out = (CV_TRANSITION_EVAL(5U, (int32_T)_SFD_CCP_CALL(5U, 0,
          chartInstance->c38_sfEvent == c38_event_endpstim != 0U,
          chartInstance->c38_sfEvent)) != 0);
        if (c38_g_out) {
          _SFD_CT_CALL(TRANSITION_ACTIVE_TAG, 5U, chartInstance->c38_sfEvent);
          chartInstance->c38_tp_stimulated = 0U;
          chartInstance->c38_is_c38_heart_model = c38_IN_NO_ACTIVE_CHILD;
          _SFD_CS_CALL(STATE_INACTIVE_TAG, 2U, chartInstance->c38_sfEvent);
          *c38_vreset = *c38_v;
          _SFD_DATA_RANGE_CHECK(*c38_vreset, 1U);
          chartInstance->c38_resetEventCounter++;
          chartInstance->c38_is_c38_heart_model = c38_IN_resting;
          _SFD_CS_CALL(STATE_ACTIVE_TAG, 1U, chartInstance->c38_sfEvent);
          chartInstance->c38_tp_resting = 1U;
          *c38_q = 1.0;
          _SFD_DATA_RANGE_CHECK_MIN_MAX(*c38_q, 0U, 1.0, 4.0);
        }
      }

      _SFD_CS_CALL(EXIT_OUT_OF_FUNCTION_TAG, 2U, chartInstance->c38_sfEvent);
      break;

     case c38_IN_upstroke:
      CV_CHART_EVAL(35, 0, 4);
      _SFD_CS_CALL(STATE_ENTER_DURING_FUNCTION_TAG, 3U,
                   chartInstance->c38_sfEvent);
      _SFD_CT_CALL(TRANSITION_BEFORE_PROCESSING_TAG, 2U,
                   chartInstance->c38_sfEvent);
      c38_h_out = (CV_TRANSITION_EVAL(2U, (int32_T)_SFD_CCP_CALL(2U, 0,
        chartInstance->c38_sfEvent == c38_event_event3 != 0U,
        chartInstance->c38_sfEvent)) != 0);
      if (c38_h_out) {
        _SFD_CT_CALL(TRANSITION_ACTIVE_TAG, 2U, chartInstance->c38_sfEvent);
        chartInstance->c38_tp_upstroke = 0U;
        chartInstance->c38_is_c38_heart_model = c38_IN_NO_ACTIVE_CHILD;
        _SFD_CS_CALL(STATE_INACTIVE_TAG, 3U, chartInstance->c38_sfEvent);
        *c38_vreset = *c38_v;
        _SFD_DATA_RANGE_CHECK(*c38_vreset, 1U);
        chartInstance->c38_resetEventCounter++;
        chartInstance->c38_is_c38_heart_model = c38_IN_plateau;
        _SFD_CS_CALL(STATE_ACTIVE_TAG, 0U, chartInstance->c38_sfEvent);
        chartInstance->c38_tp_plateau = 1U;
        *c38_q = 4.0;
        _SFD_DATA_RANGE_CHECK_MIN_MAX(*c38_q, 0U, 1.0, 4.0);
        *c38_vno = chartInstance->c38_vn;
        _SFD_DATA_RANGE_CHECK(*c38_vno, 4U);
      }

      _SFD_CS_CALL(EXIT_OUT_OF_FUNCTION_TAG, 3U, chartInstance->c38_sfEvent);
      break;

     default:
      CV_CHART_EVAL(35, 0, 0);
      chartInstance->c38_is_c38_heart_model = c38_IN_NO_ACTIVE_CHILD;
      _SFD_CS_CALL(STATE_INACTIVE_TAG, 0U, chartInstance->c38_sfEvent);
      break;
    }
  }

  _SFD_CC_CALL(EXIT_OUT_OF_FUNCTION_TAG, 35U, chartInstance->c38_sfEvent);
}

static void initSimStructsc38_heart_model(SFc38_heart_modelInstanceStruct
  *chartInstance)
{
}

static void init_script_number_translation(uint32_T c38_machineNumber, uint32_T
  c38_chartNumber)
{
}

const mxArray *sf_c38_heart_model_get_eml_resolved_functions_info(void)
{
  const mxArray *c38_nameCaptureInfo = NULL;
  c38_nameCaptureInfo = NULL;
  sf_mex_assign(&c38_nameCaptureInfo, sf_mex_create("nameCaptureInfo", NULL, 0,
    0U, 1U, 0U, 2, 0, 1), FALSE);
  return c38_nameCaptureInfo;
}

static const mxArray *c38_sf_marshallOut(void *chartInstanceVoid, void
  *c38_inData)
{
  const mxArray *c38_mxArrayOutData = NULL;
  int8_T c38_u;
  const mxArray *c38_y = NULL;
  SFc38_heart_modelInstanceStruct *chartInstance;
  chartInstance = (SFc38_heart_modelInstanceStruct *)chartInstanceVoid;
  c38_mxArrayOutData = NULL;
  c38_u = *(int8_T *)c38_inData;
  c38_y = NULL;
  sf_mex_assign(&c38_y, sf_mex_create("y", &c38_u, 2, 0U, 0U, 0U, 0), FALSE);
  sf_mex_assign(&c38_mxArrayOutData, c38_y, FALSE);
  return c38_mxArrayOutData;
}

static int8_T c38_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_u, const emlrtMsgIdentifier *c38_parentId)
{
  int8_T c38_y;
  int8_T c38_i0;
  sf_mex_import(c38_parentId, sf_mex_dup(c38_u), &c38_i0, 1, 2, 0U, 0, 0U, 0);
  c38_y = c38_i0;
  sf_mex_destroy(&c38_u);
  return c38_y;
}

static void c38_sf_marshallIn(void *chartInstanceVoid, const mxArray
  *c38_mxArrayInData, const char_T *c38_varName, void *c38_outData)
{
  const mxArray *c38_event1;
  const char_T *c38_identifier;
  emlrtMsgIdentifier c38_thisId;
  int8_T c38_y;
  SFc38_heart_modelInstanceStruct *chartInstance;
  chartInstance = (SFc38_heart_modelInstanceStruct *)chartInstanceVoid;
  c38_event1 = sf_mex_dup(c38_mxArrayInData);
  c38_identifier = c38_varName;
  c38_thisId.fIdentifier = c38_identifier;
  c38_thisId.fParent = NULL;
  c38_y = c38_emlrt_marshallIn(chartInstance, sf_mex_dup(c38_event1),
    &c38_thisId);
  sf_mex_destroy(&c38_event1);
  *(int8_T *)c38_outData = c38_y;
  sf_mex_destroy(&c38_mxArrayInData);
}

static const mxArray *c38_b_sf_marshallOut(void *chartInstanceVoid, void
  *c38_inData)
{
  const mxArray *c38_mxArrayOutData = NULL;
  uint32_T c38_u;
  const mxArray *c38_y = NULL;
  SFc38_heart_modelInstanceStruct *chartInstance;
  chartInstance = (SFc38_heart_modelInstanceStruct *)chartInstanceVoid;
  c38_mxArrayOutData = NULL;
  c38_u = *(uint32_T *)c38_inData;
  c38_y = NULL;
  sf_mex_assign(&c38_y, sf_mex_create("y", &c38_u, 7, 0U, 0U, 0U, 0), FALSE);
  sf_mex_assign(&c38_mxArrayOutData, c38_y, FALSE);
  return c38_mxArrayOutData;
}

static uint32_T c38_b_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_b_resetEventCounter, const char_T
  *c38_identifier)
{
  uint32_T c38_y;
  emlrtMsgIdentifier c38_thisId;
  c38_thisId.fIdentifier = c38_identifier;
  c38_thisId.fParent = NULL;
  c38_y = c38_c_emlrt_marshallIn(chartInstance, sf_mex_dup
    (c38_b_resetEventCounter), &c38_thisId);
  sf_mex_destroy(&c38_b_resetEventCounter);
  return c38_y;
}

static uint32_T c38_c_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_u, const emlrtMsgIdentifier *c38_parentId)
{
  uint32_T c38_y;
  uint32_T c38_u0;
  sf_mex_import(c38_parentId, sf_mex_dup(c38_u), &c38_u0, 1, 7, 0U, 0, 0U, 0);
  c38_y = c38_u0;
  sf_mex_destroy(&c38_u);
  return c38_y;
}

static void c38_b_sf_marshallIn(void *chartInstanceVoid, const mxArray
  *c38_mxArrayInData, const char_T *c38_varName, void *c38_outData)
{
  const mxArray *c38_b_resetEventCounter;
  const char_T *c38_identifier;
  emlrtMsgIdentifier c38_thisId;
  uint32_T c38_y;
  SFc38_heart_modelInstanceStruct *chartInstance;
  chartInstance = (SFc38_heart_modelInstanceStruct *)chartInstanceVoid;
  c38_b_resetEventCounter = sf_mex_dup(c38_mxArrayInData);
  c38_identifier = c38_varName;
  c38_thisId.fIdentifier = c38_identifier;
  c38_thisId.fParent = NULL;
  c38_y = c38_c_emlrt_marshallIn(chartInstance, sf_mex_dup
    (c38_b_resetEventCounter), &c38_thisId);
  sf_mex_destroy(&c38_b_resetEventCounter);
  *(uint32_T *)c38_outData = c38_y;
  sf_mex_destroy(&c38_mxArrayInData);
}

static const mxArray *c38_c_sf_marshallOut(void *chartInstanceVoid, void
  *c38_inData)
{
  const mxArray *c38_mxArrayOutData = NULL;
  int32_T c38_u;
  const mxArray *c38_y = NULL;
  SFc38_heart_modelInstanceStruct *chartInstance;
  chartInstance = (SFc38_heart_modelInstanceStruct *)chartInstanceVoid;
  c38_mxArrayOutData = NULL;
  c38_u = *(int32_T *)c38_inData;
  c38_y = NULL;
  sf_mex_assign(&c38_y, sf_mex_create("y", &c38_u, 6, 0U, 0U, 0U, 0), FALSE);
  sf_mex_assign(&c38_mxArrayOutData, c38_y, FALSE);
  return c38_mxArrayOutData;
}

static int32_T c38_d_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_u, const emlrtMsgIdentifier *c38_parentId)
{
  int32_T c38_y;
  int32_T c38_i1;
  sf_mex_import(c38_parentId, sf_mex_dup(c38_u), &c38_i1, 1, 6, 0U, 0, 0U, 0);
  c38_y = c38_i1;
  sf_mex_destroy(&c38_u);
  return c38_y;
}

static void c38_c_sf_marshallIn(void *chartInstanceVoid, const mxArray
  *c38_mxArrayInData, const char_T *c38_varName, void *c38_outData)
{
  const mxArray *c38_b_sfEvent;
  const char_T *c38_identifier;
  emlrtMsgIdentifier c38_thisId;
  int32_T c38_y;
  SFc38_heart_modelInstanceStruct *chartInstance;
  chartInstance = (SFc38_heart_modelInstanceStruct *)chartInstanceVoid;
  c38_b_sfEvent = sf_mex_dup(c38_mxArrayInData);
  c38_identifier = c38_varName;
  c38_thisId.fIdentifier = c38_identifier;
  c38_thisId.fParent = NULL;
  c38_y = c38_d_emlrt_marshallIn(chartInstance, sf_mex_dup(c38_b_sfEvent),
    &c38_thisId);
  sf_mex_destroy(&c38_b_sfEvent);
  *(int32_T *)c38_outData = c38_y;
  sf_mex_destroy(&c38_mxArrayInData);
}

static const mxArray *c38_d_sf_marshallOut(void *chartInstanceVoid, void
  *c38_inData)
{
  const mxArray *c38_mxArrayOutData = NULL;
  uint8_T c38_u;
  const mxArray *c38_y = NULL;
  SFc38_heart_modelInstanceStruct *chartInstance;
  chartInstance = (SFc38_heart_modelInstanceStruct *)chartInstanceVoid;
  c38_mxArrayOutData = NULL;
  c38_u = *(uint8_T *)c38_inData;
  c38_y = NULL;
  sf_mex_assign(&c38_y, sf_mex_create("y", &c38_u, 3, 0U, 0U, 0U, 0), FALSE);
  sf_mex_assign(&c38_mxArrayOutData, c38_y, FALSE);
  return c38_mxArrayOutData;
}

static uint8_T c38_e_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_b_tp_resting, const char_T *c38_identifier)
{
  uint8_T c38_y;
  emlrtMsgIdentifier c38_thisId;
  c38_thisId.fIdentifier = c38_identifier;
  c38_thisId.fParent = NULL;
  c38_y = c38_f_emlrt_marshallIn(chartInstance, sf_mex_dup(c38_b_tp_resting),
    &c38_thisId);
  sf_mex_destroy(&c38_b_tp_resting);
  return c38_y;
}

static uint8_T c38_f_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_u, const emlrtMsgIdentifier *c38_parentId)
{
  uint8_T c38_y;
  uint8_T c38_u1;
  sf_mex_import(c38_parentId, sf_mex_dup(c38_u), &c38_u1, 1, 3, 0U, 0, 0U, 0);
  c38_y = c38_u1;
  sf_mex_destroy(&c38_u);
  return c38_y;
}

static void c38_d_sf_marshallIn(void *chartInstanceVoid, const mxArray
  *c38_mxArrayInData, const char_T *c38_varName, void *c38_outData)
{
  const mxArray *c38_b_tp_resting;
  const char_T *c38_identifier;
  emlrtMsgIdentifier c38_thisId;
  uint8_T c38_y;
  SFc38_heart_modelInstanceStruct *chartInstance;
  chartInstance = (SFc38_heart_modelInstanceStruct *)chartInstanceVoid;
  c38_b_tp_resting = sf_mex_dup(c38_mxArrayInData);
  c38_identifier = c38_varName;
  c38_thisId.fIdentifier = c38_identifier;
  c38_thisId.fParent = NULL;
  c38_y = c38_f_emlrt_marshallIn(chartInstance, sf_mex_dup(c38_b_tp_resting),
    &c38_thisId);
  sf_mex_destroy(&c38_b_tp_resting);
  *(uint8_T *)c38_outData = c38_y;
  sf_mex_destroy(&c38_mxArrayInData);
}

static const mxArray *c38_e_sf_marshallOut(void *chartInstanceVoid, void
  *c38_inData)
{
  const mxArray *c38_mxArrayOutData = NULL;
  real_T c38_u;
  const mxArray *c38_y = NULL;
  SFc38_heart_modelInstanceStruct *chartInstance;
  chartInstance = (SFc38_heart_modelInstanceStruct *)chartInstanceVoid;
  c38_mxArrayOutData = NULL;
  c38_u = *(real_T *)c38_inData;
  c38_y = NULL;
  sf_mex_assign(&c38_y, sf_mex_create("y", &c38_u, 0, 0U, 0U, 0U, 0), FALSE);
  sf_mex_assign(&c38_mxArrayOutData, c38_y, FALSE);
  return c38_mxArrayOutData;
}

static real_T c38_g_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_q, const char_T *c38_identifier)
{
  real_T c38_y;
  emlrtMsgIdentifier c38_thisId;
  c38_thisId.fIdentifier = c38_identifier;
  c38_thisId.fParent = NULL;
  c38_y = c38_h_emlrt_marshallIn(chartInstance, sf_mex_dup(c38_q), &c38_thisId);
  sf_mex_destroy(&c38_q);
  return c38_y;
}

static real_T c38_h_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_u, const emlrtMsgIdentifier *c38_parentId)
{
  real_T c38_y;
  real_T c38_d0;
  sf_mex_import(c38_parentId, sf_mex_dup(c38_u), &c38_d0, 1, 0, 0U, 0, 0U, 0);
  c38_y = c38_d0;
  sf_mex_destroy(&c38_u);
  return c38_y;
}

static void c38_e_sf_marshallIn(void *chartInstanceVoid, const mxArray
  *c38_mxArrayInData, const char_T *c38_varName, void *c38_outData)
{
  const mxArray *c38_q;
  const char_T *c38_identifier;
  emlrtMsgIdentifier c38_thisId;
  real_T c38_y;
  SFc38_heart_modelInstanceStruct *chartInstance;
  chartInstance = (SFc38_heart_modelInstanceStruct *)chartInstanceVoid;
  c38_q = sf_mex_dup(c38_mxArrayInData);
  c38_identifier = c38_varName;
  c38_thisId.fIdentifier = c38_identifier;
  c38_thisId.fParent = NULL;
  c38_y = c38_h_emlrt_marshallIn(chartInstance, sf_mex_dup(c38_q), &c38_thisId);
  sf_mex_destroy(&c38_q);
  *(real_T *)c38_outData = c38_y;
  sf_mex_destroy(&c38_mxArrayInData);
}

static boolean_T c38_i_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_reset, const char_T *c38_identifier)
{
  boolean_T c38_y;
  emlrtMsgIdentifier c38_thisId;
  c38_thisId.fIdentifier = c38_identifier;
  c38_thisId.fParent = NULL;
  c38_y = c38_j_emlrt_marshallIn(chartInstance, sf_mex_dup(c38_reset),
    &c38_thisId);
  sf_mex_destroy(&c38_reset);
  return c38_y;
}

static boolean_T c38_j_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_u, const emlrtMsgIdentifier *c38_parentId)
{
  boolean_T c38_y;
  boolean_T c38_b0;
  sf_mex_import(c38_parentId, sf_mex_dup(c38_u), &c38_b0, 1, 11, 0U, 0, 0U, 0);
  c38_y = c38_b0;
  sf_mex_destroy(&c38_u);
  return c38_y;
}

static const mxArray *c38_k_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_b_setSimStateSideEffectsInfo, const char_T *
  c38_identifier)
{
  const mxArray *c38_y = NULL;
  emlrtMsgIdentifier c38_thisId;
  c38_y = NULL;
  c38_thisId.fIdentifier = c38_identifier;
  c38_thisId.fParent = NULL;
  sf_mex_assign(&c38_y, c38_l_emlrt_marshallIn(chartInstance, sf_mex_dup
    (c38_b_setSimStateSideEffectsInfo), &c38_thisId), FALSE);
  sf_mex_destroy(&c38_b_setSimStateSideEffectsInfo);
  return c38_y;
}

static const mxArray *c38_l_emlrt_marshallIn(SFc38_heart_modelInstanceStruct
  *chartInstance, const mxArray *c38_u, const emlrtMsgIdentifier *c38_parentId)
{
  const mxArray *c38_y = NULL;
  c38_y = NULL;
  sf_mex_assign(&c38_y, sf_mex_duplicatearraysafe(&c38_u), FALSE);
  sf_mex_destroy(&c38_u);
  return c38_y;
}

static void init_dsm_address_info(SFc38_heart_modelInstanceStruct *chartInstance)
{
}

/* SFunction Glue Code */
void sf_c38_heart_model_get_check_sum(mxArray *plhs[])
{
  ((real_T *)mxGetPr((plhs[0])))[0] = (real_T)(3302892405U);
  ((real_T *)mxGetPr((plhs[0])))[1] = (real_T)(558966526U);
  ((real_T *)mxGetPr((plhs[0])))[2] = (real_T)(2786346244U);
  ((real_T *)mxGetPr((plhs[0])))[3] = (real_T)(1849611249U);
}

mxArray *sf_c38_heart_model_get_autoinheritance_info(void)
{
  const char *autoinheritanceFields[] = { "checksum", "inputs", "parameters",
    "outputs", "locals" };

  mxArray *mxAutoinheritanceInfo = mxCreateStructMatrix(1,1,5,
    autoinheritanceFields);

  {
    mxArray *mxChecksum = mxCreateString("CXxzStyR2vNHDGvRqLjuwG");
    mxSetField(mxAutoinheritanceInfo,0,"checksum",mxChecksum);
  }

  {
    const char *dataFields[] = { "size", "type", "complexity" };

    mxArray *mxData = mxCreateStructMatrix(1,1,3,dataFields);

    {
      mxArray *mxSize = mxCreateDoubleMatrix(1,2,mxREAL);
      double *pr = mxGetPr(mxSize);
      pr[0] = (double)(1);
      pr[1] = (double)(1);
      mxSetField(mxData,0,"size",mxSize);
    }

    {
      const char *typeFields[] = { "base", "fixpt" };

      mxArray *mxType = mxCreateStructMatrix(1,1,2,typeFields);
      mxSetField(mxType,0,"base",mxCreateDoubleScalar(10));
      mxSetField(mxType,0,"fixpt",mxCreateDoubleMatrix(0,0,mxREAL));
      mxSetField(mxData,0,"type",mxType);
    }

    mxSetField(mxData,0,"complexity",mxCreateDoubleScalar(0));
    mxSetField(mxAutoinheritanceInfo,0,"inputs",mxData);
  }

  {
    mxSetField(mxAutoinheritanceInfo,0,"parameters",mxCreateDoubleMatrix(0,0,
                mxREAL));
  }

  {
    const char *dataFields[] = { "size", "type", "complexity" };

    mxArray *mxData = mxCreateStructMatrix(1,3,3,dataFields);

    {
      mxArray *mxSize = mxCreateDoubleMatrix(1,2,mxREAL);
      double *pr = mxGetPr(mxSize);
      pr[0] = (double)(1);
      pr[1] = (double)(1);
      mxSetField(mxData,0,"size",mxSize);
    }

    {
      const char *typeFields[] = { "base", "fixpt" };

      mxArray *mxType = mxCreateStructMatrix(1,1,2,typeFields);
      mxSetField(mxType,0,"base",mxCreateDoubleScalar(10));
      mxSetField(mxType,0,"fixpt",mxCreateDoubleMatrix(0,0,mxREAL));
      mxSetField(mxData,0,"type",mxType);
    }

    mxSetField(mxData,0,"complexity",mxCreateDoubleScalar(0));

    {
      mxArray *mxSize = mxCreateDoubleMatrix(1,2,mxREAL);
      double *pr = mxGetPr(mxSize);
      pr[0] = (double)(1);
      pr[1] = (double)(1);
      mxSetField(mxData,1,"size",mxSize);
    }

    {
      const char *typeFields[] = { "base", "fixpt" };

      mxArray *mxType = mxCreateStructMatrix(1,1,2,typeFields);
      mxSetField(mxType,0,"base",mxCreateDoubleScalar(10));
      mxSetField(mxType,0,"fixpt",mxCreateDoubleMatrix(0,0,mxREAL));
      mxSetField(mxData,1,"type",mxType);
    }

    mxSetField(mxData,1,"complexity",mxCreateDoubleScalar(0));

    {
      mxArray *mxSize = mxCreateDoubleMatrix(1,2,mxREAL);
      double *pr = mxGetPr(mxSize);
      pr[0] = (double)(1);
      pr[1] = (double)(1);
      mxSetField(mxData,2,"size",mxSize);
    }

    {
      const char *typeFields[] = { "base", "fixpt" };

      mxArray *mxType = mxCreateStructMatrix(1,1,2,typeFields);
      mxSetField(mxType,0,"base",mxCreateDoubleScalar(10));
      mxSetField(mxType,0,"fixpt",mxCreateDoubleMatrix(0,0,mxREAL));
      mxSetField(mxData,2,"type",mxType);
    }

    mxSetField(mxData,2,"complexity",mxCreateDoubleScalar(0));
    mxSetField(mxAutoinheritanceInfo,0,"outputs",mxData);
  }

  {
    mxSetField(mxAutoinheritanceInfo,0,"locals",mxCreateDoubleMatrix(0,0,mxREAL));
  }

  return(mxAutoinheritanceInfo);
}

static const mxArray *sf_get_sim_state_info_c38_heart_model(void)
{
  const char *infoFields[] = { "chartChecksum", "varInfo" };

  mxArray *mxInfo = mxCreateStructMatrix(1, 1, 2, infoFields);
  const char *infoEncStr[] = {
    "100 S1x8'type','srcId','name','auxInfo'{{M[1],M[3],T\"q\",},{M[1],M[41],T\"vno\",},{M[1],M[21],T\"vreset\",},{M[3],M[39],T\"vn\",},{M[6],M[20],T\"reset\",},{M[7],M[20],T\"resetEventCounter\",},{M[8],M[0],T\"is_active_c38_heart_model\",},{M[9],M[0],T\"is_c38_heart_model\",}}"
  };

  mxArray *mxVarInfo = sf_mex_decode_encoded_mx_struct_array(infoEncStr, 8, 10);
  mxArray *mxChecksum = mxCreateDoubleMatrix(1, 4, mxREAL);
  sf_c38_heart_model_get_check_sum(&mxChecksum);
  mxSetField(mxInfo, 0, infoFields[0], mxChecksum);
  mxSetField(mxInfo, 0, infoFields[1], mxVarInfo);
  return mxInfo;
}

static void chart_debug_initialization(SimStruct *S, unsigned int
  fullDebuggerInitialization)
{
  if (!sim_mode_is_rtw_gen(S)) {
    SFc38_heart_modelInstanceStruct *chartInstance;
    chartInstance = (SFc38_heart_modelInstanceStruct *) ((ChartInfoStruct *)
      (ssGetUserData(S)))->chartInstance;
    if (ssIsFirstInitCond(S) && fullDebuggerInitialization==1) {
      /* do this only if simulation is starting */
      {
        unsigned int chartAlreadyPresent;
        chartAlreadyPresent = sf_debug_initialize_chart
          (_heart_modelMachineNumber_,
           38,
           4,
           7,
           5,
           6,
           0,
           0,
           0,
           0,
           &(chartInstance->chartNumber),
           &(chartInstance->instanceNumber),
           ssGetPath(S),
           (void *)S);
        if (chartAlreadyPresent==0) {
          /* this is the first instance */
          init_script_number_translation(_heart_modelMachineNumber_,
            chartInstance->chartNumber);
          sf_debug_set_chart_disable_implicit_casting(_heart_modelMachineNumber_,
            chartInstance->chartNumber,1);
          sf_debug_set_chart_event_thresholds(_heart_modelMachineNumber_,
            chartInstance->chartNumber,
            6,
            6,
            6);
          _SFD_SET_DATA_PROPS(0,2,0,1,"q");
          _SFD_SET_DATA_PROPS(1,2,0,1,"vreset");
          _SFD_SET_DATA_PROPS(2,1,1,0,"v");
          _SFD_SET_DATA_PROPS(3,0,0,0,"vn");
          _SFD_SET_DATA_PROPS(4,2,0,1,"vno");
          _SFD_EVENT_SCOPE(0,1);
          _SFD_EVENT_SCOPE(1,2);
          _SFD_EVENT_SCOPE(2,1);
          _SFD_EVENT_SCOPE(3,1);
          _SFD_EVENT_SCOPE(4,1);
          _SFD_EVENT_SCOPE(5,1);
          _SFD_STATE_INFO(0,0,0);
          _SFD_STATE_INFO(1,0,0);
          _SFD_STATE_INFO(2,0,0);
          _SFD_STATE_INFO(3,0,0);
          _SFD_CH_SUBSTATE_COUNT(4);
          _SFD_CH_SUBSTATE_DECOMP(0);
          _SFD_CH_SUBSTATE_INDEX(0,0);
          _SFD_CH_SUBSTATE_INDEX(1,1);
          _SFD_CH_SUBSTATE_INDEX(2,2);
          _SFD_CH_SUBSTATE_INDEX(3,3);
          _SFD_ST_SUBSTATE_COUNT(0,0);
          _SFD_ST_SUBSTATE_COUNT(1,0);
          _SFD_ST_SUBSTATE_COUNT(2,0);
          _SFD_ST_SUBSTATE_COUNT(3,0);
        }

        _SFD_CV_INIT_CHART(4,1,0,0);

        {
          _SFD_CV_INIT_STATE(0,0,0,0,0,0,NULL,NULL);
        }

        {
          _SFD_CV_INIT_STATE(1,0,0,0,0,0,NULL,NULL);
        }

        {
          _SFD_CV_INIT_STATE(2,0,0,0,0,0,NULL,NULL);
        }

        {
          _SFD_CV_INIT_STATE(3,0,0,0,0,0,NULL,NULL);
        }

        {
          static unsigned int sStartGuardMap[] = { 0 };

          static unsigned int sEndGuardMap[] = { 8 };

          static int sPostFixPredicateTree[] = { 0 };

          _SFD_CV_INIT_TRANS(5,1,&(sStartGuardMap[0]),&(sEndGuardMap[0]),1,
                             &(sPostFixPredicateTree[0]));
        }

        _SFD_CV_INIT_TRANS(3,0,NULL,NULL,0,NULL);

        {
          static unsigned int sStartGuardMap[] = { 0 };

          static unsigned int sEndGuardMap[] = { 5 };

          static int sPostFixPredicateTree[] = { 0 };

          _SFD_CV_INIT_TRANS(4,1,&(sStartGuardMap[0]),&(sEndGuardMap[0]),1,
                             &(sPostFixPredicateTree[0]));
        }

        {
          static unsigned int sStartGuardMap[] = { 0 };

          static unsigned int sEndGuardMap[] = { 6 };

          static int sPostFixPredicateTree[] = { 0 };

          _SFD_CV_INIT_TRANS(6,1,&(sStartGuardMap[0]),&(sEndGuardMap[0]),1,
                             &(sPostFixPredicateTree[0]));
        }

        {
          static unsigned int sStartGuardMap[] = { 0 };

          static unsigned int sEndGuardMap[] = { 6 };

          static int sPostFixPredicateTree[] = { 0 };

          _SFD_CV_INIT_TRANS(0,1,&(sStartGuardMap[0]),&(sEndGuardMap[0]),1,
                             &(sPostFixPredicateTree[0]));
        }

        {
          static unsigned int sStartGuardMap[] = { 0 };

          static unsigned int sEndGuardMap[] = { 6 };

          static int sPostFixPredicateTree[] = { 0 };

          _SFD_CV_INIT_TRANS(1,1,&(sStartGuardMap[0]),&(sEndGuardMap[0]),1,
                             &(sPostFixPredicateTree[0]));
        }

        {
          static unsigned int sStartGuardMap[] = { 0 };

          static unsigned int sEndGuardMap[] = { 6 };

          static int sPostFixPredicateTree[] = { 0 };

          _SFD_CV_INIT_TRANS(2,1,&(sStartGuardMap[0]),&(sEndGuardMap[0]),1,
                             &(sPostFixPredicateTree[0]));
        }

        _SFD_TRANS_COV_WTS(5,0,1,0,2);
        if (chartAlreadyPresent==0) {
          static unsigned int sStartGuardMap[] = { 0 };

          static unsigned int sEndGuardMap[] = { 8 };

          _SFD_TRANS_COV_MAPS(5,
                              0,NULL,NULL,
                              1,&(sStartGuardMap[0]),&(sEndGuardMap[0]),
                              0,NULL,NULL,
                              2,NULL,NULL);
        }

        _SFD_TRANS_COV_WTS(3,0,0,0,0);
        if (chartAlreadyPresent==0) {
          _SFD_TRANS_COV_MAPS(3,
                              0,NULL,NULL,
                              0,NULL,NULL,
                              0,NULL,NULL,
                              0,NULL,NULL);
        }

        _SFD_TRANS_COV_WTS(4,0,1,0,3);
        if (chartAlreadyPresent==0) {
          static unsigned int sStartGuardMap[] = { 0 };

          static unsigned int sEndGuardMap[] = { 5 };

          _SFD_TRANS_COV_MAPS(4,
                              0,NULL,NULL,
                              1,&(sStartGuardMap[0]),&(sEndGuardMap[0]),
                              0,NULL,NULL,
                              3,NULL,NULL);
        }

        _SFD_TRANS_COV_WTS(6,0,1,0,2);
        if (chartAlreadyPresent==0) {
          static unsigned int sStartGuardMap[] = { 0 };

          static unsigned int sEndGuardMap[] = { 6 };

          _SFD_TRANS_COV_MAPS(6,
                              0,NULL,NULL,
                              1,&(sStartGuardMap[0]),&(sEndGuardMap[0]),
                              0,NULL,NULL,
                              2,NULL,NULL);
        }

        _SFD_TRANS_COV_WTS(0,0,1,0,2);
        if (chartAlreadyPresent==0) {
          static unsigned int sStartGuardMap[] = { 0 };

          static unsigned int sEndGuardMap[] = { 6 };

          _SFD_TRANS_COV_MAPS(0,
                              0,NULL,NULL,
                              1,&(sStartGuardMap[0]),&(sEndGuardMap[0]),
                              0,NULL,NULL,
                              2,NULL,NULL);
        }

        _SFD_TRANS_COV_WTS(1,0,1,0,2);
        if (chartAlreadyPresent==0) {
          static unsigned int sStartGuardMap[] = { 0 };

          static unsigned int sEndGuardMap[] = { 6 };

          _SFD_TRANS_COV_MAPS(1,
                              0,NULL,NULL,
                              1,&(sStartGuardMap[0]),&(sEndGuardMap[0]),
                              0,NULL,NULL,
                              2,NULL,NULL);
        }

        _SFD_TRANS_COV_WTS(2,0,1,0,2);
        if (chartAlreadyPresent==0) {
          static unsigned int sStartGuardMap[] = { 0 };

          static unsigned int sEndGuardMap[] = { 6 };

          _SFD_TRANS_COV_MAPS(2,
                              0,NULL,NULL,
                              1,&(sStartGuardMap[0]),&(sEndGuardMap[0]),
                              0,NULL,NULL,
                              2,NULL,NULL);
        }

        _SFD_SET_DATA_COMPILED_PROPS(0,SF_DOUBLE,0,NULL,0,0,0,0.0,1.0,0,0,
          (MexFcnForType)c38_e_sf_marshallOut,(MexInFcnForType)
          c38_e_sf_marshallIn);
        _SFD_SET_DATA_COMPILED_PROPS(1,SF_DOUBLE,0,NULL,0,0,0,0.0,1.0,0,0,
          (MexFcnForType)c38_e_sf_marshallOut,(MexInFcnForType)
          c38_e_sf_marshallIn);
        _SFD_SET_DATA_COMPILED_PROPS(2,SF_DOUBLE,0,NULL,0,0,0,0.0,1.0,0,0,
          (MexFcnForType)c38_e_sf_marshallOut,(MexInFcnForType)NULL);
        _SFD_SET_DATA_COMPILED_PROPS(3,SF_DOUBLE,0,NULL,0,0,0,0.0,1.0,0,0,
          (MexFcnForType)c38_e_sf_marshallOut,(MexInFcnForType)
          c38_e_sf_marshallIn);
        _SFD_SET_DATA_COMPILED_PROPS(4,SF_DOUBLE,0,NULL,0,0,0,0.0,1.0,0,0,
          (MexFcnForType)c38_e_sf_marshallOut,(MexInFcnForType)
          c38_e_sf_marshallIn);

        {
          real_T *c38_q;
          real_T *c38_vreset;
          real_T *c38_v;
          real_T *c38_vno;
          c38_vno = (real_T *)ssGetOutputPortSignal(chartInstance->S, 3);
          c38_v = (real_T *)ssGetInputPortSignal(chartInstance->S, 0);
          c38_vreset = (real_T *)ssGetOutputPortSignal(chartInstance->S, 2);
          c38_q = (real_T *)ssGetOutputPortSignal(chartInstance->S, 1);
          _SFD_SET_DATA_VALUE_PTR(0U, c38_q);
          _SFD_SET_DATA_VALUE_PTR(1U, c38_vreset);
          _SFD_SET_DATA_VALUE_PTR(2U, c38_v);
          _SFD_SET_DATA_VALUE_PTR(3U, &chartInstance->c38_vn);
          _SFD_SET_DATA_VALUE_PTR(4U, c38_vno);
        }
      }
    } else {
      sf_debug_reset_current_state_configuration(_heart_modelMachineNumber_,
        chartInstance->chartNumber,chartInstance->instanceNumber);
    }
  }
}

static const char* sf_get_instance_specialization()
{
  return "VLGMxtOF1PxqZXftUpTlcH";
}

static void sf_opaque_initialize_c38_heart_model(void *chartInstanceVar)
{
  chart_debug_initialization(((SFc38_heart_modelInstanceStruct*)
    chartInstanceVar)->S,0);
  initialize_params_c38_heart_model((SFc38_heart_modelInstanceStruct*)
    chartInstanceVar);
  initialize_c38_heart_model((SFc38_heart_modelInstanceStruct*) chartInstanceVar);
}

static void sf_opaque_enable_c38_heart_model(void *chartInstanceVar)
{
  enable_c38_heart_model((SFc38_heart_modelInstanceStruct*) chartInstanceVar);
}

static void sf_opaque_disable_c38_heart_model(void *chartInstanceVar)
{
  disable_c38_heart_model((SFc38_heart_modelInstanceStruct*) chartInstanceVar);
}

static void sf_opaque_gateway_c38_heart_model(void *chartInstanceVar)
{
  sf_c38_heart_model((SFc38_heart_modelInstanceStruct*) chartInstanceVar);
}

extern const mxArray* sf_internal_get_sim_state_c38_heart_model(SimStruct* S)
{
  ChartInfoStruct *chartInfo = (ChartInfoStruct*) ssGetUserData(S);
  mxArray *plhs[1] = { NULL };

  mxArray *prhs[4];
  int mxError = 0;
  prhs[0] = mxCreateString("chart_simctx_raw2high");
  prhs[1] = mxCreateDoubleScalar(ssGetSFuncBlockHandle(S));
  prhs[2] = (mxArray*) get_sim_state_c38_heart_model
    ((SFc38_heart_modelInstanceStruct*)chartInfo->chartInstance);/* raw sim ctx */
  prhs[3] = (mxArray*) sf_get_sim_state_info_c38_heart_model();/* state var info */
  mxError = sf_mex_call_matlab(1, plhs, 4, prhs, "sfprivate");
  mxDestroyArray(prhs[0]);
  mxDestroyArray(prhs[1]);
  mxDestroyArray(prhs[2]);
  mxDestroyArray(prhs[3]);
  if (mxError || plhs[0] == NULL) {
    sf_mex_error_message("Stateflow Internal Error: \nError calling 'chart_simctx_raw2high'.\n");
  }

  return plhs[0];
}

extern void sf_internal_set_sim_state_c38_heart_model(SimStruct* S, const
  mxArray *st)
{
  ChartInfoStruct *chartInfo = (ChartInfoStruct*) ssGetUserData(S);
  mxArray *plhs[1] = { NULL };

  mxArray *prhs[4];
  int mxError = 0;
  prhs[0] = mxCreateString("chart_simctx_high2raw");
  prhs[1] = mxCreateDoubleScalar(ssGetSFuncBlockHandle(S));
  prhs[2] = mxDuplicateArray(st);      /* high level simctx */
  prhs[3] = (mxArray*) sf_get_sim_state_info_c38_heart_model();/* state var info */
  mxError = sf_mex_call_matlab(1, plhs, 4, prhs, "sfprivate");
  mxDestroyArray(prhs[0]);
  mxDestroyArray(prhs[1]);
  mxDestroyArray(prhs[2]);
  mxDestroyArray(prhs[3]);
  if (mxError || plhs[0] == NULL) {
    sf_mex_error_message("Stateflow Internal Error: \nError calling 'chart_simctx_high2raw'.\n");
  }

  set_sim_state_c38_heart_model((SFc38_heart_modelInstanceStruct*)
    chartInfo->chartInstance, mxDuplicateArray(plhs[0]));
  mxDestroyArray(plhs[0]);
}

static const mxArray* sf_opaque_get_sim_state_c38_heart_model(SimStruct* S)
{
  return sf_internal_get_sim_state_c38_heart_model(S);
}

static void sf_opaque_set_sim_state_c38_heart_model(SimStruct* S, const mxArray *
  st)
{
  sf_internal_set_sim_state_c38_heart_model(S, st);
}

static void sf_opaque_terminate_c38_heart_model(void *chartInstanceVar)
{
  if (chartInstanceVar!=NULL) {
    SimStruct *S = ((SFc38_heart_modelInstanceStruct*) chartInstanceVar)->S;
    if (sim_mode_is_rtw_gen(S) || sim_mode_is_external(S)) {
      sf_clear_rtw_identifier(S);
    }

    finalize_c38_heart_model((SFc38_heart_modelInstanceStruct*) chartInstanceVar);
    free((void *)chartInstanceVar);
    ssSetUserData(S,NULL);
  }

  unload_heart_model_optimization_info();
}

static void sf_opaque_init_subchart_simstructs(void *chartInstanceVar)
{
  initSimStructsc38_heart_model((SFc38_heart_modelInstanceStruct*)
    chartInstanceVar);
}

extern unsigned int sf_machine_global_initializer_called(void);
static void mdlProcessParameters_c38_heart_model(SimStruct *S)
{
  int i;
  for (i=0;i<ssGetNumRunTimeParams(S);i++) {
    if (ssGetSFcnParamTunable(S,i)) {
      ssUpdateDlgParamAsRunTimeParam(S,i);
    }
  }

  if (sf_machine_global_initializer_called()) {
    initialize_params_c38_heart_model((SFc38_heart_modelInstanceStruct*)
      (((ChartInfoStruct *)ssGetUserData(S))->chartInstance));
  }
}

static void mdlSetWorkWidths_c38_heart_model(SimStruct *S)
{
  if (sim_mode_is_rtw_gen(S) || sim_mode_is_external(S)) {
    mxArray *infoStruct = load_heart_model_optimization_info();
    int_T chartIsInlinable =
      (int_T)sf_is_chart_inlinable(S,sf_get_instance_specialization(),infoStruct,
      38);
    ssSetStateflowIsInlinable(S,chartIsInlinable);
    ssSetRTWCG(S,sf_rtw_info_uint_prop(S,sf_get_instance_specialization(),
                infoStruct,38,"RTWCG"));
    ssSetEnableFcnIsTrivial(S,1);
    ssSetDisableFcnIsTrivial(S,1);
    ssSetNotMultipleInlinable(S,sf_rtw_info_uint_prop(S,
      sf_get_instance_specialization(),infoStruct,38,
      "gatewayCannotBeInlinedMultipleTimes"));
    if (chartIsInlinable) {
      ssSetInputPortOptimOpts(S, 0, SS_REUSABLE_AND_LOCAL);
      sf_mark_chart_expressionable_inputs(S,sf_get_instance_specialization(),
        infoStruct,38,1);
      sf_mark_chart_reusable_outputs(S,sf_get_instance_specialization(),
        infoStruct,38,4);
    }

    ssSetInputPortOptimOpts(S, 1, SS_REUSABLE_AND_LOCAL);
    sf_set_rtw_dwork_info(S,sf_get_instance_specialization(),infoStruct,38);
    ssSetHasSubFunctions(S,!(chartIsInlinable));
  } else {
  }

  ssSetOptions(S,ssGetOptions(S)|SS_OPTION_WORKS_WITH_CODE_REUSE);
  ssSetChecksum0(S,(2458450781U));
  ssSetChecksum1(S,(698416506U));
  ssSetChecksum2(S,(1871434388U));
  ssSetChecksum3(S,(986029849U));
  ssSetmdlDerivatives(S, NULL);
  ssSetExplicitFCSSCtrl(S,1);
}

static void mdlRTW_c38_heart_model(SimStruct *S)
{
  if (sim_mode_is_rtw_gen(S)) {
    ssWriteRTWStrParam(S, "StateflowChartType", "Stateflow");
  }
}

static void mdlStart_c38_heart_model(SimStruct *S)
{
  SFc38_heart_modelInstanceStruct *chartInstance;
  chartInstance = (SFc38_heart_modelInstanceStruct *)malloc(sizeof
    (SFc38_heart_modelInstanceStruct));
  memset(chartInstance, 0, sizeof(SFc38_heart_modelInstanceStruct));
  if (chartInstance==NULL) {
    sf_mex_error_message("Could not allocate memory for chart instance.");
  }

  chartInstance->chartInfo.chartInstance = chartInstance;
  chartInstance->chartInfo.isEMLChart = 0;
  chartInstance->chartInfo.chartInitialized = 0;
  chartInstance->chartInfo.sFunctionGateway = sf_opaque_gateway_c38_heart_model;
  chartInstance->chartInfo.initializeChart =
    sf_opaque_initialize_c38_heart_model;
  chartInstance->chartInfo.terminateChart = sf_opaque_terminate_c38_heart_model;
  chartInstance->chartInfo.enableChart = sf_opaque_enable_c38_heart_model;
  chartInstance->chartInfo.disableChart = sf_opaque_disable_c38_heart_model;
  chartInstance->chartInfo.getSimState = sf_opaque_get_sim_state_c38_heart_model;
  chartInstance->chartInfo.setSimState = sf_opaque_set_sim_state_c38_heart_model;
  chartInstance->chartInfo.getSimStateInfo =
    sf_get_sim_state_info_c38_heart_model;
  chartInstance->chartInfo.zeroCrossings = NULL;
  chartInstance->chartInfo.outputs = NULL;
  chartInstance->chartInfo.derivatives = NULL;
  chartInstance->chartInfo.mdlRTW = mdlRTW_c38_heart_model;
  chartInstance->chartInfo.mdlStart = mdlStart_c38_heart_model;
  chartInstance->chartInfo.mdlSetWorkWidths = mdlSetWorkWidths_c38_heart_model;
  chartInstance->chartInfo.extModeExec = NULL;
  chartInstance->chartInfo.restoreLastMajorStepConfiguration = NULL;
  chartInstance->chartInfo.restoreBeforeLastMajorStepConfiguration = NULL;
  chartInstance->chartInfo.storeCurrentConfiguration = NULL;
  chartInstance->S = S;
  ssSetUserData(S,(void *)(&(chartInstance->chartInfo)));/* register the chart instance with simstruct */
  init_dsm_address_info(chartInstance);
  if (!sim_mode_is_rtw_gen(S)) {
  }

  sf_opaque_init_subchart_simstructs(chartInstance->chartInfo.chartInstance);
  chart_debug_initialization(S,1);
}

void c38_heart_model_method_dispatcher(SimStruct *S, int_T method, void *data)
{
  switch (method) {
   case SS_CALL_MDL_START:
    mdlStart_c38_heart_model(S);
    break;

   case SS_CALL_MDL_SET_WORK_WIDTHS:
    mdlSetWorkWidths_c38_heart_model(S);
    break;

   case SS_CALL_MDL_PROCESS_PARAMETERS:
    mdlProcessParameters_c38_heart_model(S);
    break;

   default:
    /* Unhandled method */
    sf_mex_error_message("Stateflow Internal Error:\n"
                         "Error calling c38_heart_model_method_dispatcher.\n"
                         "Can't handle method %d.\n", method);
    break;
  }
}
