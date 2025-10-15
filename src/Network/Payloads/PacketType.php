<?php

declare(strict_types=1);

namespace AvionBlock\VoiceCraft\Network\Payloads;

enum PacketType : int {
	case Login = 0;
	case Logout = 1;
	case Accept = 2;
	case Deny = 3;
	case Bind = 4;
	case Update = 5;
	case AckUpdate = 6;
	case GetChannels = 7;
	case GetChannelSettings = 8;
	case SetChannelSettings = 9;
	case GetDefaultSettings = 10;
	case SetDefaultSettings = 11;

	//Participant Stuff
	case GetParticipants = 12;
	case DisconnectParticipant = 13;
	case GetParticipantBitmask = 14;
	case SetParticipantBitmask = 15;
	case MuteParticipant = 16;
	case UnmuteParticipant = 17;
	case DeafenParticipant = 18;
	case UndeafenParticipant = 19;

	case ANDModParticipantBitmask = 20;
	case ORModParticipantBitmask = 21;
	case XORModParticipantBitmask = 22;

	case ChannelMove = 23;
};
