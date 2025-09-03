<?php

namespace bitpvp\interceptor;

enum InterceptorResult {

	case INTERCEPTION_CONTINUE;
	case INTERCEPTION_ABORT;
}